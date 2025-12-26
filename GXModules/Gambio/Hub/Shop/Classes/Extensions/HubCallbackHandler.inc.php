<?php
/* --------------------------------------------------------------
   HubCallbackHandler.inc.php 2023-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;
use \HubPublic\ValueObjects\AuthHash;
use \HubPublic\ValueObjects\HubClientKey;
use \HubPublic\ValueObjects\HubSessionKey;
use Gambio\Core\Cache\Services\CacheFactory;
use League\Container\Exception\NotFoundException;

/**
 * Class HubCallbackHandler
 */
class HubCallbackHandler implements HubCallbackHandlerInterface
{
	/**
	 * Hub service factory
	 *
	 * @var HubServiceFactoryInterface
	 */
	protected $hubServiceFactory;

	/**
	 * Hub client key configuration
	 *
	 * @var HubClientKeyConfigurationInterface
	 */
	protected $hubClientKeyConfiguration;

	/**
	 * Order status Color
	 *
	 * @var string
	 */
	protected $orderStatusColor = '897b65';

	/**
	 * Curl Request
	 *
	 * @var \HubPublic\Http\CurlRequest
	 */
	protected $curlRequest;

	/**
	 * IP-list URL
	 *
	 * @var string
	 */
	protected $ipListUrl = '';

    /**
     * Indicates legacy mode (old configuration tables)
     *
     * @var bool
     */
    private $isLegacyConfiguration;

	/**
	 * HubCallbackHandler constructor.
	 *
	 * @param \HubServiceFactoryInterface         $hubServiceFactory
	 * @param \HubClientKeyConfigurationInterface $hubClientKeyConfiguration
	 * @param \HubPublic\Http\CurlRequest         $curlRequest
	 * @param                                     $ipListUrl
	 */
	public function __construct(HubServiceFactoryInterface $hubServiceFactory,
	                            HubClientKeyConfigurationInterface $hubClientKeyConfiguration,
	                            CurlRequest $curlRequest,
	                            $ipListUrl)
    {
        $this->hubServiceFactory         = $hubServiceFactory;
        $this->hubClientKeyConfiguration = $hubClientKeyConfiguration;
        $this->curlRequest               = $curlRequest;
        $this->ipListUrl                 = $ipListUrl;
        $this->isLegacyConfiguration     = version_compare(gm_get_conf('INSTALLED_VERSION'), '4.1') < 0;
    }


	/**
	 * Stores the hub client key and the shop key, sends a http response code header and returns a json response array.
	 *
	 * @param string $authHash
	 * @param string $clientKey
	 * @param string $shopKey
	 *
	 * @return array Json response array
	 */
	public function proceedClientKeyCallback($authHash, $clientKey, $shopKey)
	{
		try
		{
			$this->_validateCallbackRequest();

			$authHash  = new AuthHash($authHash);
			$clientKey = new HubClientKey($clientKey);

			if(!$this->_authByHash($authHash))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$this->hubClientKeyConfiguration->set($clientKey);

            if ($this->isLegacyConfiguration) {
                $query = 'UPDATE `configuration`
					  SET `configuration_value` = "' . xtc_db_input($shopKey) . '"
					  WHERE `configuration_key` = "GAMBIO_SHOP_KEY"';
            } else {
                $query = 'UPDATE `gx_configurations`
					  SET `value` = "' . xtc_db_input($shopKey) . '"
					  WHERE `key` = "GAMBIO_SHOP_KEY"';
            }

			xtc_db_query($query);

			$this->_activateGambioHubPaymentModule();

			$response = ['success' => true];

			http_response_code(201);
		}
		catch(Exception $e)
		{
			$code = $e->getCode() !== 0 ? $e->getCode() : 500;

			$response = [
				'code'    => $code,
				'message' => $e->getMessage(),
			];

			http_response_code($code);
		}

		return $response;
	}


	/**
	 * Stores the hub session key, sends a http response code header and returns a json response array.
	 *
	 * @param string $authHash
	 * @param string $sessionKey
	 *
	 * @return array Json Response Array
	 */
	public function proceedSessionKeyCallback($authHash, $sessionKey)
	{
		try
		{
			$this->_validateCallbackRequest();

			$sessionKey = new HubSessionKey($sessionKey);
			$authHash   = new AuthHash($authHash);

			if(!$this->_authByHash($authHash))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$hubSessionKeyService = $this->hubServiceFactory->createHubSessionKeyService();
			$hubSessionKeyService->store($sessionKey, $authHash);

			$response = ['success' => true];

			http_response_code(201);
		}
		catch(Exception $e)
		{
			$code = $e->getCode() !== 0 ? $e->getCode() : 500;

			$response = [
				'code'    => $code,
				'message' => $e->getMessage()
			];

			http_response_code($code);
		}

		return $response;
	}


	/**
	 * Updates the status of an order, sends a http response code header and resurns a json response array.
	 *
	 * @param string $clientKey
	 * @param int    $orderId
	 * @param int    $orderStatusId
	 *
	 * @return array Json Response Array
	 */
	public function proceedUpdateOrderStatusCallback($clientKey, $orderId, $orderStatusId)
	{
		$orderId       = (int)$orderId;
		$orderStatusId = (int)$orderStatusId;

		try
		{
			$this->_validateCallbackRequest();

			if(!$this->_authByClientKey($clientKey))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$query  = 'SELECT COUNT(*) AS `cnt` FROM `orders` WHERE `orders_id` = ' . $orderId;
			$result = xtc_db_query($query);
			$row    = xtc_db_fetch_array($result);

			if($row['cnt'] !== '1')
			{
				throw new UnexpectedValueException("Order with ID $orderId does not exist");
			}

			$validOrderStatusId = $orderStatusId;

			// Check for valid order status ID
			$query  = 'SELECT COUNT(*) AS `cnt` FROM `orders_status` WHERE `orders_status_id` = ' . $orderStatusId;
			$result = xtc_db_query($query);
			$row    = xtc_db_fetch_array($result);

			if((int)$row['cnt'] < 1)
			{
				$logControl = LogControl::get_instance();
				$logControl->warning('Unknown order status with ID ' . $orderStatusId, 'hub', 'errors');
				$validOrderStatusId = (int)DEFAULT_ORDERS_STATUS_ID;
			}

			// Update Orders Table
			$query = '
				UPDATE `orders`
				SET `orders_status` = ' . $validOrderStatusId . '
				WHERE `orders_id` = ' . $orderId;

			xtc_db_query($query);

			// Update Orders History Table
			$query = 'INSERT INTO `orders_status_history`
						SET
							`orders_id` = ' . $orderId . ',
							`orders_status_id` = ' . $validOrderStatusId . ',
							`date_added` = "' . date('Y-m-d H:i:s') . '",
							`customer_notified` = 0';

			xtc_db_query($query);

			http_response_code(200);

			$response = ['success' => true];
		}
		catch(Exception $e)
		{
			$code = $e->getCode() !== 0 ? $e->getCode() : 500;

			$response = [
				'code'    => $code,
				'message' => $e->getMessage()
			];

			http_response_code($code);
		}

		return $response;
	}


	/**
	 * Inserts into gm_configuration or updates gm_configuration with given key and value, sends a http response code
	 * header and returns a json response array.
	 *
	 * @param string $clientKey          HubClientKey
	 * @param string $configurationKey   Determines which gm_configuration key should be updated
	 * @param string $configurationValue Determines to which value the key should be set
	 *
	 * @return array Json Response Array
	 */
	public function proceedUpdateConfiguration($clientKey, $configurationKey, $configurationValue)
	{
		try
		{
			$this->_validateCallbackRequest();

			if(!$this->_authByClientKey($clientKey))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$configValue = xtc_db_prepare_input($configurationValue);

			if(strpos($configValue, 'base64:') === 0)
			{
				$configValue = base64_decode(substr($configValue, 7));
			}

            if ($this->isLegacyConfiguration) {
                $query = 'REPLACE INTO `gm_configuration`
					  SET
					    `gm_key` = "' . xtc_db_input(xtc_db_prepare_input($configurationKey)) . '",
					    `gm_value` = "' . xtc_db_input($configValue) . '"';
            } else {
                $query = 'REPLACE INTO `gx_configurations`
					  SET
					    `key` = "gm_configuration/' . xtc_db_input(xtc_db_prepare_input($configurationKey)) . '",
					    `value` = "' . xtc_db_input($configValue) . '"';
            }

			xtc_db_query($query);

			if($configurationKey === 'GAMBIO_HUB_CLIENT_KEY' && $configurationValue === '')
			{
				$this->_deactivateGambioHubPaymentModule();
			}
			
			$this->clearPayPalSettingsCache();

			http_response_code(200);

			$response = ['success' => true];
		}
		catch(Exception $e)
		{
			$code = $e->getCode() !== 0 ? $e->getCode() : 500;

			$response = [
				'code'    => $code,
				'message' => $e->getMessage()
			];

			http_response_code($code);
		}

		return $response;
	}


	/**
	 * Reads from gm_configuration with given key, sends a http response code header and returns a json response array.
	 *
	 * @param string $clientKey        HubClientKey
	 * @param string $configurationKey Determines which gm_configuration key should be retrieved
	 *
	 * @return array Json Response Array
	 */
	public function proceedGetConfiguration($clientKey, $configurationKey)
	{
		try
		{
			$this->_validateCallbackRequest();

			if(!$this->_authByClientKey($clientKey))
			{
				throw new RuntimeException('Forbidden', 403);
			}

            $value = '';
            if ($this->isLegacyConfiguration) {
                $query = 'SELECT `gm_value` AS `value`
					  FROM `gm_configuration`
					  WHERE `gm_key` = "' . xtc_db_input($configurationKey) . '"';
            } else {
                $query = 'SELECT `value`
					  FROM `gx_configurations`
					  WHERE `key` = "gm_configuration/' . xtc_db_input($configurationKey) . '"';
            }
            $result = xtc_db_query($query);
            if (xtc_db_num_rows($result)) {
                $result = xtc_db_fetch_array($result);
                $value  = $result['value'];
            }

			http_response_code(200);

			$response = ['success' => true, 'value' => $value];
		}
		catch(Exception $e)
		{
			$code = $e->getCode() !== 0 ? $e->getCode() : 500;

			$response = [
				'code'    => $code,
				'message' => $e->getMessage()
			];

			http_response_code($code);
		}

		return $response;
	}


	/**
	 * Inserts a new order status name, sends a http response code header and returns a json response array.
	 *
	 * @param string $clientKey        Client Key.
	 * @param array  $orderStatusArray Order status array.
	 *
	 * @return array  Json Response Array
	 */
	public function proceedCreateOrderStatusCallback($clientKey, $orderStatusArray)
	{
		try
		{
			$this->_validateCallbackRequest();

			if(!$this->_authByClientKey($clientKey))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$result             = xtc_db_query('SELECT * FROM `orders_status` ORDER BY `orders_status_id` DESC');
			$lastOrdersStatusId = xtc_db_fetch_array($result);
			$newOrdersStatusId  = (int)$lastOrdersStatusId['orders_status_id'] + 1;

			$query         = 'SELECT `languages_id`, `code` FROM ' . TABLE_LANGUAGES;
			$langResult    = xtc_db_query($query);
			$queries       = [];
			$createRecords = true;

			while($row = xtc_db_fetch_array($langResult))
			{
				$orderStatusName = array_key_exists($row['code'],
				                                    $orderStatusArray) ? $orderStatusArray[$row['code']] : $orderStatusArray[key($orderStatusArray)];

				$query = 'SELECT `orders_status_id`
							FROM `orders_status`
							WHERE
								`orders_status_name` = "' . xtc_db_input($orderStatusName) . '" AND
								language_id = ' . $row['languages_id'];

				$result = xtc_db_query($query);

				if(xtc_db_num_rows($result) === 0 || $row['code'] !== DEFAULT_LANGUAGE)
				{
					$query = 'INSERT INTO `orders_status`
								SET
									`orders_status_id` = ' . $newOrdersStatusId . ',
									`language_id` = ' . $row['languages_id'] . ',
									`orders_status_name` = "' . xtc_db_input($orderStatusName) . '",
									`color` = "' . xtc_db_input($this->orderStatusColor) . '"';

					$queries[] = $query;
				}
				elseif($row['code'] === DEFAULT_LANGUAGE)
				{
					$createRecords = false;

					break;
				}
			}

			if($createRecords)
			{
				foreach($queries as $query)
				{
					xtc_db_query($query);
				}

				http_response_code(201);
			}
			else
			{
				http_response_code(200);
			}

			$response = ['success' => true];
		}
		catch(Exception $e)
		{
			$code = $e->getCode() !== 0 ? $e->getCode() : 500;

			$response = [
				'code'    => $code,
				'message' => $e->getMessage()
			];

			http_response_code($code);
		}

		return $response;
	}


	/**
	 * Authenticates the request with the "X-Auth-Hash" header.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash
	 *
	 * @return bool The authentication result.
	 */
	protected function _authByHash(AuthHash $authHash)
	{
		$hubAuthService = $this->hubServiceFactory->createHubAuthService();

		return $hubAuthService->authByAuthHash($authHash);
	}


	/**
	 * Authenticates the request with the "X-Client-Key" header.
	 *
	 * @param string $clientKey
	 *
	 * @return bool The authentication result.
	 */
	protected function _authByClientKey($clientKey)
	{
		return $this->hubClientKeyConfiguration->get() === $clientKey;
	}


	/**
	 * Validates the IP.
	 *
	 * @param array $ipList
	 */
	protected function _isIpValid(array $ipList)
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

		if(empty($ip))
		{
			throw new RuntimeException('Forbidden', 403);
		}

		$valid = false;
		foreach($ipList as $hubIp)
		{
			if($hubIp === '*' || strpos($ip, $hubIp) === 0)
			{
				$valid = true;
				break;
			}
		}

		// Check with IP whitelist (comma separated IP values).
		if(!$valid && defined('MODULE_PAYMENT_GAMBIO_HUB_IP_WHITELIST'))
		{
			$ipWhitelist = explode(',', MODULE_PAYMENT_GAMBIO_HUB_IP_WHITELIST);

			foreach($ipWhitelist as $ipWhitelistEntry)
			{
				if(empty($ipWhitelistEntry))
				{
					continue;
				}

				// Will also match partial IP values like "192.168.0".
				if($ipWhitelistEntry === '*' || strpos($ip, trim($ipWhitelistEntry)) !== false)
				{
					$valid = true;
					break;
				}
			}
		}

		if(!$valid)
		{
			throw new RuntimeException('Forbidden', 403);
		}
	}


	/**
	 * Validate request IP
	 *
	 * @return $this
	 *
	 * @throws \Exception if Callback
	 */
	protected function _validateCallbackRequest()
	{
		$response = $this->_getIpList();

		if($response->getStatusCode() === 200)
		{
			$ipList = @json_decode($response->getBody(), true);

			if(!is_array($ipList))
			{
				$response = $this->_getIpList(true); // retry without cache
				$ipList   = @json_decode($response->getBody(), true);
			}

			if(is_array($ipList))
			{
				try
				{
					$this->_isIpValid($ipList);
				}
				catch(RuntimeException $exception)
				{
					$response = $this->_getIpList(true); // retry without cache
					$ipList   = @json_decode($response->getBody(), true);
					$this->_isIpValid($ipList);
				}
			}
		}

		return $this;
	}


	/**
	 * Returns the IP list data (either from cache or from a cURL request).
	 *
	 * The Hub Connector accepts many callbacks which will trigger many requests to the IP lists file of the Hub
	 * servers. In order to save some requests the contents of the IP list must be stored in cache and only be called
	 * when no cached data were found.
	 *
	 * @param bool $bypassCacheValue Disable cache when fetching the list.
	 *
	 * @return \HubPublic\ValueObjects\HttpResponse
	 */
	protected function _getIpList($bypassCacheValue = false)
	{
		$dataCache = DataCache::get_instance();
		$cacheKey  = 'hub-ip-list';

		if($dataCache->key_exists($cacheKey, true) && !$bypassCacheValue)
		{
			$response = $dataCache->get_persistent_data($cacheKey);

			if(isset($response['__expires']))
			{
				$response = $response['__expires'] > new DateTime ? unserialize($response['value']) : $this->_getIpList(true);
			}
		}
		else
		{
			$response = $this->curlRequest->setUrl($this->ipListUrl)->execute();

			if($response->getStatusCode() === 200)
			{
				$ipListCache = [
					'__expires' => new DateTime('tomorrow'),
					'value'     => serialize($response)
				];

				$dataCache->write_persistent_data($cacheKey, $ipListCache);
			}
		}

		return $response;
	}


	/**
	 * Activates the gambio_hub payment module
	 */
    protected function _activateGambioHubPaymentModule()
    {
        if ($this->isLegacyConfiguration) {
            $query = 'UPDATE `configuration`
					  SET `configuration_value` = "True"
					  WHERE `configuration_key` = "MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            xtc_db_query($query);
        } else {
            $query = 'UPDATE `gx_configurations`
					  SET `value` = "True"
					  WHERE `key` = "configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            xtc_db_query($query);
        }
	}


	/**
	 * Deactivates the gambio_hub payment module
	 */
    protected function _deactivateGambioHubPaymentModule()
    {
        if ($this->isLegacyConfiguration) {
            $query = 'UPDATE `configuration`
					  SET `configuration_value` = "False"
					  WHERE `configuration_key` = "MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            xtc_db_query($query);
        } else {
            $query = 'UPDATE `gx_configurations`
					  SET `value` = "False"
					  WHERE `key` = "configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            xtc_db_query($query);
        }
    }
    
    
    protected function clearPayPalSettingsCache()
    {
        $cache = null;
        if (class_exists(LegacyDependencyContainer::class)) {
            try {
                /** @var CacheFactory $cacheFactory */
                $cacheFactory = LegacyDependencyContainer::getInstance()->get(CacheFactory::class);
                $cache = $cacheFactory->createCacheFor('paypal2hub');
            } catch (NotFoundException $e) {
            }
        }
        if ($cache !== null) {
            $cache->clear();
            return;
        }
        // else … i. e. legacy cache is used
        $cacheFile = DIR_FS_CATALOG . 'cache/paypal2hub.json.cache';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
}
