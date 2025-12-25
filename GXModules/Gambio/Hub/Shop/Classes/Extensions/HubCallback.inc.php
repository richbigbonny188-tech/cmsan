<?php
/* --------------------------------------------------------------
   HubCallback.inc.php 2023-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;
use \HubPublic\ValueObjects\AuthHash;
use \HubPublic\ValueObjects\HubClientKey;
use \HubPublic\ValueObjects\HubSessionKey;
use \Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Core\Cache\Services\CacheFactory;
use League\Container\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class HubCallback
 *
 * Used for handling gambio_hub_callback.php callbacks introduced with Hub Connector v1.9.0 (Hub v12.0).
 *
 * IMPORTANT: This class is completely independent from the shop. Overloading does not work.
 */
class HubCallback
{
	/**
	 * Curl Request
	 *
	 * @var \HubPublic\Http\CurlRequest
	 */
	private $curlRequest;

	/**
	 * DB connection
	 *
	 * @var mysqli
	 */
	private $dbLink;

	/**
	 * Order status Color
	 *
	 * @var string
	 */
	private $orderStatusColor = '897b65';

    /**
     * Indicates legacy mode (old configuration tables)
     *
     * @var bool
     */
	private $isLegacyConfiguration;

	/**
	 * HubCallback constructor.
	 *
	 * @param \HubPublic\Http\CurlRequest $curlRequest
	 */
	public function __construct(CurlRequest $curlRequest)
	{
		$this->curlRequest = $curlRequest;
		$this->isLegacyConfiguration = false;
        $result = $this->_query("SHOW TABLES LIKE 'gx_configurations'");
        if (empty($result->num_rows)) {
            $this->isLegacyConfiguration = true;
        }
	}


	/**
	 * Handles the callback end echos json response.
	 */
	public function proceed()
	{
		switch($_GET['action'])
		{
			case 'client_key':
				if(!array_key_exists('HTTP_X_AUTH_HASH', $_SERVER) || !array_key_exists('clientKey', $_POST)
				   || !array_key_exists('shopKey', $_POST))
				{
					$response = [
						'code'    => 400,
						'message' => 'parameters are missing',
					];

					http_response_code(400);
				}
				else
				{
					$response = $this->_actionClientKeyCallback($_SERVER['HTTP_X_AUTH_HASH'], $_POST['clientKey'],
					                                            $_POST['shopKey']);
				}

				break;
			case 'session_key':
				if(!array_key_exists('HTTP_X_AUTH_HASH', $_SERVER) || !array_key_exists('sessionKey', $_POST))
				{
					$response = [
						'code'    => 400,
						'message' => 'parameters are missing',
					];

					http_response_code(400);
				}
				else
				{
					$response = $this->_actionSessionKeyCallback($_SERVER['HTTP_X_AUTH_HASH'], $_POST['sessionKey']);
				}

				break;
			case 'get_configuration':
				if(!array_key_exists('HTTP_X_CLIENT_KEY', $_SERVER) || !array_key_exists('keys', $_GET))
				{
					$response = [
						'code'    => 400,
						'message' => 'parameters are missing',
					];

					http_response_code(400);
				}
				else
				{
					$response = $this->_actionGetConfiguration($_SERVER['HTTP_X_CLIENT_KEY'], $_GET['keys']);
				}

				break;
			case 'update_configuration':
				if(!array_key_exists('HTTP_X_CLIENT_KEY', $_SERVER) || !array_key_exists('configuration', $_POST))
				{
					$response = [
						'code'    => 400,
						'message' => 'parameters are missing',
					];

					http_response_code(400);
				}
				else
				{
					$response = $this->_actionUpdateConfiguration($_SERVER['HTTP_X_CLIENT_KEY'],
					                                              $_POST['configuration']);
				}

				break;
			case 'update_order_status':
				if(!array_key_exists('HTTP_X_CLIENT_KEY', $_SERVER) || !array_key_exists('orderId', $_POST)
				   || !array_key_exists('orderStatusId', $_POST))
				{
					$response = [
						'code'    => 400,
						'message' => 'parameters are missing',
					];

					http_response_code(400);
				}
				else
				{
				    $comment = isset($_POST['orderStatusComment']) ? (string)$_POST['orderStatusComment'] : '';
					$response = $this->_actionUpdateOrderStatusCallback($_SERVER['HTTP_X_CLIENT_KEY'],
					                                                    $_POST['orderId'], $_POST['orderStatusId'], $comment);
				}

				break;
			case 'create_order_status':
				if(!array_key_exists('HTTP_X_CLIENT_KEY', $_SERVER) || !array_key_exists('order_status_name', $_POST))
				{
					$response = [
						'code'    => 400,
						'message' => 'parameters are missing',
					];

					http_response_code(400);
				}
				else
				{
					$response = $this->_actionCreateOrderStatusCallback($_SERVER['HTTP_X_CLIENT_KEY'],
					                                                    $_POST['order_status_name']);
				}

				break;
			case 'clear_cache':
				if(!array_key_exists('HTTP_X_CLIENT_KEY', $_SERVER))
				{
					$response = [
						'code'    => 403,
						'message' => 'Forbidden',
					];

					http_response_code(403);
				}
				else
				{
					$response = $this->_actionClearCache($_SERVER['HTTP_X_CLIENT_KEY']);
				}

				break;
            case 'update_payment_instructions':
                $orderId             = isset($_POST['order_id']) ? (int)$_POST['order_id'] : false;
                $paymentInstructions = json_decode($_POST['payment_instructions'], true);
                if (!array_key_exists('HTTP_X_CLIENT_KEY', $_SERVER) || $orderId === false
                    || !is_array($paymentInstructions)) {
                    $response = [
                        'code'    => 403,
                        'message' => 'Forbidden',
                    ];
                    http_response_code($response['code']);
                } else {
                    $response = $this->actionUpdatePaymentInstructions($_SERVER['HTTP_X_CLIENT_KEY'], $orderId, $paymentInstructions);
                }
                break;
			default:
				$response = [
					'code'    => 500,
					'message' => 'unknown action',
				];

				http_response_code(500);
		}

		header('Content-Type: application/json');

		echo json_encode($response, JSON_UNESCAPED_SLASHES);
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
	private function _actionClientKeyCallback($authHash, $clientKey, $shopKey)
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

            if ($this->isLegacyConfiguration) {
                $this->_query('
                    REPLACE INTO `gm_configuration`
                    SET
                        `gm_key` = "GAMBIO_HUB_CLIENT_KEY",
                        `gm_value` = "' . $this->_realEscapeString($clientKey->asString()) . '"');

                $this->_query('
                    UPDATE `configuration`
                    SET
                        `configuration_value` = "' . $this->_realEscapeString($shopKey) . '"
                    WHERE
                        `configuration_key` = "GAMBIO_SHOP_KEY"
                ');
            } else {
                $this->_query('
                    UPDATE `gx_configurations`
                    SET
                      `value` = "' . $this->_realEscapeString($clientKey->asString()) . '"
                    WHERE
                      `key` = "gm_configuration/GAMBIO_HUB_CLIENT_KEY"
                ');

                $this->_query('
                    UPDATE `gx_configurations`
                    SET `value` = "' . $this->_realEscapeString($shopKey) . '"
                    WHERE `key` = "configuration/GAMBIO_SHOP_KEY"
                ');
            }

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
	 * Updates the status of an order, sends a http response code header and resurns a json response array.
	 *
	 * @param string $clientKey
	 * @param int    $orderId
	 * @param int    $orderStatusId
	 *
	 * @return array Json Response Array
	 */
	private function _actionUpdateOrderStatusCallback($clientKey, $orderId, $orderStatusId, $comment = '')
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
			$result = $this->_query($query);
			$row    = mysqli_fetch_assoc($result);

			if($row['cnt'] !== '1')
			{
				throw new UnexpectedValueException("Order with ID $orderId does not exist");
			}

			$validOrderStatusId = $orderStatusId;

			// Check for valid order status ID
			$query  = 'SELECT COUNT(*) AS `cnt` FROM `orders_status` WHERE `orders_status_id` = ' . $orderStatusId;
			$result = $this->_query($query);
			$row    = mysqli_fetch_assoc($result);

			if((int)$row['cnt'] < 1)
			{
                if ($this->isLegacyConfiguration) {
                    $query = 'SELECT `configuration_value` AS `value` FROM `configuration` WHERE `configuration_key` = "DEFAULT_ORDERS_STATUS_ID"';
                } else {
                    $query = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/DEFAULT_ORDERS_STATUS_ID"';
                }
                $result             = $this->_query($query);
				$validOrderStatusId = (int)mysqli_fetch_assoc($result)['value'];

				$this->_logOrderUpdateStatusFallback($orderId, $orderStatusId, $validOrderStatusId);
			}

			// Update Orders Table
			$query = '
				UPDATE `orders`
				SET `orders_status` = ' . $validOrderStatusId . '
				WHERE `orders_id` = ' . $orderId;

			$this->_query($query);

			// Update Orders History Table
			$query = 'INSERT INTO `orders_status_history`
						SET
							`orders_id` = ' . $orderId . ',
							`orders_status_id` = ' . $validOrderStatusId . ',
							`date_added` = "' . date('Y-m-d H:i:s') . '",
							`comments` = \'' . $this->_realEscapeString($comment) . '\',
							`customer_notified` = 0';
            
            $this->_query($query);

			http_response_code(200);

			$response = ['success' => true];

            if (stripos($comment, 'Capture Completed') !== false) {
                require_once DIR_FS_CATALOG . '/includes/application_top_main.php';
                $paymentDetailsProvider = MainFactory::create('PayPal2HubPaymentDetailsProvider');
                $paymentDetails = $paymentDetailsProvider->getDetails(new \IdType($orderId));
                if (!isset($paymentDetails['hubdetails']['error']) && !empty($paymentDetails['hubdetails']['order']))
                {
                    $order = $paymentDetails['hubdetails']['order'];
                    $paymentSource = array_keys($order['payment_source'])[0];
                    $apmPaymentSources = ['bancontact', 'blik', 'eps', 'giropay', 'ideal', 'mybank', 'przelewy24', 'sofort', 'pui'];
                    if (in_array($paymentSource, $apmPaymentSources, true)) {
                        $this->sendOrderConfirmationMail($orderId);
                    }
                }
            }
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
     * Sends order confirmation mail
     * 
     * @param int $orderId
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function sendOrderConfirmationMail(int $orderId): bool
    {
        $this->_log("Sending order confirmation for order {$orderId}");
        /** @var OrderReadService $orderReadService */
        $orderReadService = StaticGXCoreLoader::getService('OrderRead');
        $order = $orderReadService->getOrderById(new \IdType($orderId));
        $orderLanguageCode = strtolower($order->getLanguageCode()->asString());
        
        $sessionLanguagesId     = $_SESSION['languages_id'];
        $sessionLanguage        = $_SESSION['language'];
        $sessionLanguageCode    = $_SESSION['language_code'];
        $sessionLanguageCharset = $_SESSION['language_charset'];
        
        $languageReadService = LegacyDependencyContainer::getInstance()->get(LanguageReadService::class);
        try {
            $orderLanguage = $languageReadService->getLanguageByCode($orderLanguageCode);
        } catch (LanguageNotFoundException $e) {
            return false;
        }
        
        $_SESSION['languages_id']     = $orderLanguage->id();
        $_SESSION['language']         = $orderLanguage->name();
        $_SESSION['language_code']    = $orderLanguage->code();
        $_SESSION['language_charset'] = $orderLanguage->charset();
        
        $sendOrderProcess = MainFactory::create_object('SendOrderProcess');
        $sendOrderProcess->set_('order_id', $orderId);
        $success = $sendOrderProcess->proceed();
        
        $_SESSION['languages_id']     = $sessionLanguagesId;
        $_SESSION['language']         = $sessionLanguage;
        $_SESSION['language_code']    = $sessionLanguageCode;
        $_SESSION['language_charset'] = $sessionLanguageCharset;
        
        return $success;
        
    }
    
    
	/**
	 * Stores the hub session key, sends a http response code header and returns a json response array.
	 *
	 * @param string $authHash
	 * @param string $sessionKey
	 *
	 * @return array Json Response Array
	 */
	private function _actionSessionKeyCallback($authHash, $sessionKey)
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

			$this->_storeSessionKey($authHash, $sessionKey);

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
	 * Reads from gm_configuration with given keys, sends a http response code header and returns a json response array.
	 *
	 * @param string $clientKey         HubClientKey
	 * @param array  $configurationKeys Determines which gm_configuration keys should be retrieved
	 *
	 * @return array Json Response Array
	 */
	private function _actionGetConfiguration($clientKey, array $configurationKeys)
	{
		try
		{
			$this->_validateCallbackRequest();

			if(!$this->_authByClientKey($clientKey))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$configuration = [];

			$configurationCache = (array)$this->_getCacheValue('hub-gm-configuration');
			$useCache           = true;
			$keyValues          = [];
			foreach($configurationKeys as $key)
			{
				if(array_key_exists($key, $configurationCache))
				{
					$configuration[$key] = $configurationCache[$key];
				}
				else
				{
					$useCache = false;
				}
			}

			if(!$useCache)
			{
			    $prefix = $this->isLegacyConfiguration ? '' : 'gm_configuration/';

				foreach($configurationKeys as $key)
				{
					$keyValues[] = '"' . $prefix . $this->_realEscapeString($key) . '"';
				}

                if ($this->isLegacyConfiguration) {
                    $result = $this->_query('SELECT `gm_key` AS `key`, `gm_value` AS `value`
					  FROM `gm_configuration`
					  WHERE `gm_key` IN (' . implode(',', $keyValues) . ')');
                } else {
                    $result = $this->_query('SELECT `key`, `value`
					  FROM `gx_configurations`
					  WHERE `key` IN (' . implode(',', $keyValues) . ')');
                }

                while($row = mysqli_fetch_assoc($result))
				{
					$configuration[$row['key']]      = $row['value'];
					$configurationCache[$row['key']] = $row['value'];
				}

				$this->_setCacheValue('hub-gm-configuration', $configurationCache);
			}

			http_response_code(200);

			$response = ['success' => true, 'configuration' => $configuration];
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
	 * Inserts into gm_configuration or updates gm_configuration with given configuration key/values, sends a http
	 * response code header and returns a json response array.
	 *
	 * @param string $clientKey     HubClientKey
	 * @param array  $configuration Configuration to be set
	 *
	 * @return array Json Response Array
	 */
	private function _actionUpdateConfiguration($clientKey, array $configuration)
	{
		try
		{
			$this->_validateCallbackRequest();

			if(!$this->_authByClientKey($clientKey))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$configurationCache = (array)$this->_getCacheValue('hub-gm-configuration');

			foreach($configuration as $key => $value)
			{
				if(strpos($value, 'base64:') === 0)
				{
					$value = base64_decode(substr($value, 7));
				}

                if ($this->isLegacyConfiguration) {
                    $this->_query('
                        REPLACE INTO `gm_configuration`
                        SET
                            `gm_key` = "' . $this->_realEscapeString($key) . '",
                            `gm_value` = "' . $this->_realEscapeString($value) . '"
                    ');
                } else {
                    $result = $this->_query('SELECT * FROM `gx_configurations` WHERE `key` = "gm_configuration/' . $this->_realEscapeString($key) . '"');

                    $row = mysqli_fetch_row($result);

                    if ($row) {
                        $this->_query('
                            UPDATE `gx_configurations`
                            SET
                                `value` = "' . $this->_realEscapeString($value) . '"
                            WHERE
                                `key` = "gm_configuration/' . $this->_realEscapeString($key) . '"
                        ');
                    } else {
                        $this->_query('
                            INSERT INTO `gx_configurations` (`key`, `value`) VALUES
                            ("gm_configuration/' . $this->_realEscapeString($key) . '", "' . $this->_realEscapeString($value) . '")
                        ');
                    }
                }

                if($key === 'GAMBIO_HUB_CLIENT_KEY' && $value === '')
				{
					$this->_deactivateGambioHubPaymentModule();
				}

				$configurationCache[$key] = $value;
			}
			
			// Clear the menu cache so that menu items get automatically updated (e.g. toggle Klarna Settlements). 
            $adminMenu = LegacyDependencyContainer::getInstance()->get(AdminMenuService::class);
            $adminMenu->deleteMenuCache();

			$this->_setCacheValue('hub-gm-configuration', $configurationCache);

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
	 * Inserts a new order status name, sends a http response code header and returns a json response array.
	 *
	 * @param string $clientKey        Client Key.
	 * @param array  $orderStatusArray Order status array.
	 *
	 * @return array  Json Response Array
	 */
	private function _actionCreateOrderStatusCallback($clientKey, $orderStatusArray)
	{
		try
		{
			$this->_validateCallbackRequest();

			if(!$this->_authByClientKey($clientKey))
			{
				throw new RuntimeException('Forbidden', 403);
			}

			$result             = $this->_query('SELECT * FROM `orders_status` ORDER BY `orders_status_id` DESC');
			$lastOrdersStatusId = mysqli_fetch_assoc($result);
			$newOrdersStatusId  = (int)$lastOrdersStatusId['orders_status_id'] + 1;

			$query         = 'SELECT `languages_id`, `code` FROM `languages`';
			$langResult    = $this->_query($query);
			$queries       = [];
			$createRecords = true;

			while($row = mysqli_fetch_assoc($langResult))
			{
				$orderStatusName = array_key_exists($row['code'],
				                                    $orderStatusArray) ? $orderStatusArray[$row['code']] : $orderStatusArray[key($orderStatusArray)];

				$query = 'SELECT `orders_status_id`
							FROM `orders_status`
							WHERE
								`orders_status_name` = "' . $this->_realEscapeString($orderStatusName) . '" AND
								language_id = ' . $row['languages_id'];

				$result = $this->_query($query);

				if(mysqli_num_rows($result) === 0 || $row['code'] !== $this->_getDefaultLanguageCode())
				{
					$query = 'INSERT INTO `orders_status`
								SET
									`orders_status_id` = ' . $newOrdersStatusId . ',
									`language_id` = ' . $row['languages_id'] . ',
									`orders_status_name` = "' . $this->_realEscapeString($orderStatusName) . '",
									`color` = "' . $this->_realEscapeString($this->orderStatusColor) . '"';

					$queries[] = $query;
				}
				elseif($row['code'] === $this->_getDefaultLanguageCode())
				{
					$createRecords = false;

					break;
				}
			}

			if($createRecords)
			{
				foreach($queries as $query)
				{
					$this->_query($query);
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
	 * Clears the hub callback cache.
	 *
	 * @param string $clientKey Client Key.
	 *
	 * @return array  Json Response Array
	 */
	private function _actionClearCache($clientKey)
	{
        try {
            $this->_validateCallbackRequest();
        } catch (CurlRequestException $e) {
            $response = [
                'code'    => 500,
                'message' => 'Internal Server Error'
            ];
            
            http_response_code(500);
            
            return $response;
        }

		if(!$this->_authByClientKey($clientKey))
		{
			$response = [
				'code'    => 403,
				'message' => 'Forbidden'
			];

			http_response_code(403);

			return $response;
		}
        
        if (file_exists($this->_getCacheFilePath('hub-client-key-validation'))) {
            @unlink($this->_getCacheFilePath('hub-client-key-validation'));
        }
        
        if (file_exists($this->_getCacheFilePath('hub-ip-list'))) {
            @unlink($this->_getCacheFilePath('hub-ip-list'));
        }
        
        if (file_exists($this->_getCacheFilePath('hub-gm-configuration'))) {
            @unlink($this->_getCacheFilePath('hub-gm-configuration'));
        }

		http_response_code(200);

		$response = ['success' => true];

		return $response;
	}
    
    
    /**
     * Adds/updates payment instructions far an order.
     * 
     * @param string $clientKey
     * @param int    $orderId
     * @param array  $paymentInstruction
     *
     * @return true[]
     */
    private function actionUpdatePaymentInstructions(string $clientKey, int $orderId, array $paymentInstruction)
    {
        try
        {
            $this->_validateCallbackRequest();
        
            if(!$this->_authByClientKey($clientKey))
            {
                throw new RuntimeException('Forbidden', 403);
            }
    
            $reference     = $this->_realEscapeString($paymentInstruction['reference'] ?? '');
            if (empty($reference)) {
                return ['success' => false, 'reason' => 'reference is required'];
            }
            $bankName      = $this->_realEscapeString($paymentInstruction['bank_name'] ?? '');
            $accountHolder = $this->_realEscapeString($paymentInstruction['account_holder'] ?? '');
            $iban          = $this->_realEscapeString($paymentInstruction['iban'] ?? '');
            $bic           = $this->_realEscapeString($paymentInstruction['bic'] ?? '');
            $value         = (float)($paymentInstruction['value'] ?? 0.0);
            $currency      = $this->_realEscapeString($paymentInstruction['currency'] ?? '');
            $dueDate       = $this->_realEscapeString($paymentInstruction['due_date'] ?? '');
    
            $instructionsQuery  = "SELECT * FROM `orders_payment_instruction` WHERE `orders_id` = {$orderId}";
            $instructionsResult = $this->_query($instructionsQuery);
            $updated = false;
            while ($row = mysqli_fetch_assoc($instructionsResult)) {
                if ($row['reference'] === $paymentInstruction['reference']) {
                    $updateQuery = "UPDATE `orders_payment_instruction` SET " .
                                   "`bank_name` = '{$bankName}', " .
                                   "`account_holder` = '{$accountHolder}', " .
                                   "`iban` = '{$iban}', " .
                                   "`bic` = '{$bic}', " .
                                   "`value` = {$value}, " .
                                   "`currency` = '{$currency}', " .
                                   "`due_date` = '{$dueDate}' " .
                                   "WHERE `orders_payment_instruction_id` = {$row['orders_payment_instruction_id']}";
                    $this->_query($updateQuery);
                    $updated = true;
                }
            }
            if (!$updated) {
                $insertQuery = "INSERT INTO `orders_payment_instruction` SET " .
                               "`orders_id` = {$orderId}, " .
                               "`reference` = '{$reference}', " .
                               "`bank_name` = '{$bankName}', " .
                               "`account_holder` = '{$accountHolder}', " .
                               "`iban` = '{$iban}', " .
                               "`bic` = '{$bic}', " .
                               "`value` = {$value}, " .
                               "`currency` = '{$currency}', " .
                               "`due_date` = '{$dueDate}' ";
                $this->_query($insertQuery);
            }
    
            return ['success'  => true,];
        }
        catch(Exception $e)
        {
            $this->_log("Failed to update payment instructions for order {$orderId}. Data received from Hub:\n"
                        . print_r($paymentInstruction, true));
            $code = $e->getCode() !== 0 ? $e->getCode() : 500;
    
            $response = [
                'code'    => $code,
                'message' => $e->getMessage()
            ];
    
            http_response_code($code);
            
            return $response;
        }
    }

    
	/**
	 * Returns the default language code
	 *
	 * @return string
	 */
    private function _getDefaultLanguageCode()
    {
        if ($this->isLegacyConfiguration) {
            $result = $this->_query('SELECT `configuration_value`
                                FROM `configuration`
                                WHERE `configuration_key` = "DEFAULT_LANGUAGE"');

            return mysqli_fetch_assoc($result)['configuration_value'];
        } else {
            $result = $this->_query('SELECT `value`
                                FROM `gx_configurations`
                                WHERE `key` = "configuration/DEFAULT_LANGUAGE"');

            return mysqli_fetch_assoc($result)['value'];
        }
    }


    /**
	 * Activates the gambio_hub payment module
	 */
	private function _activateGambioHubPaymentModule()
	{
        if ($this->isLegacyConfiguration) {
            $query = 'UPDATE `configuration`
					  SET `configuration_value` = "True"
					  WHERE `configuration_key` = "MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            $this->_query($query);
        } else {
            $query = 'UPDATE `gx_configurations`
					  SET `value` = "True"
					  WHERE `key` = "configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            $this->_query($query);
        }
	}


	/**
	 * Deactivates the gambio_hub payment module
	 */
    private function _deactivateGambioHubPaymentModule()
    {
        if ($this->isLegacyConfiguration) {
            $query = 'UPDATE `configuration`
					  SET `configuration_value` = "False"
					  WHERE `configuration_key` = "MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            $this->_query($query);
        } else {
            $query = 'UPDATE `gx_configurations`
					  SET `value` = "False"
					  WHERE `key` = "configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS"';
            $this->_query($query);
        }
    }


	/**
	 * Authenticates the request with the "X-Client-Key" header.
	 *
	 * @param string $clientKey
	 *
	 * @return bool The authentication result.
	 */
	private function _authByClientKey($clientKey)
	{
		$clientKeyValidationResult = $this->_getCacheValue('hub-client-key-validation');
		if($clientKeyValidationResult === null || !array_key_exists($clientKey, $clientKeyValidationResult))
		{
            if ($this->isLegacyConfiguration) {
                $result = $this->_query('SELECT COUNT(*) AS cnt
                                    FROM `gm_configuration`
                                    WHERE
                                    `gm_key` = "GAMBIO_HUB_CLIENT_KEY" AND
                                    `gm_value` = "' . $this->_realEscapeString($clientKey) . '"');
            } else {
                $result = $this->_query('SELECT COUNT(*) AS cnt
                                    FROM `gx_configurations`
                                    WHERE
                                    `key` = "gm_configuration/GAMBIO_HUB_CLIENT_KEY" AND
                                    `value` = "' . $this->_realEscapeString($clientKey) . '"');
            }

			$row                       = mysqli_fetch_assoc($result);
			$clientKeyValidationResult = [$clientKey => !empty($row['cnt'])];

			if($clientKeyValidationResult[$clientKey])
			{
				$this->_setCacheValue('hub-client-key-validation', $clientKeyValidationResult);
			}
		}

		return $clientKeyValidationResult[$clientKey];
	}


	/**
	 * Authenticates the request with the "X-Auth-Hash" header.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash
	 *
	 * @return bool The authentication result.
	 */
	private function _authByHash(AuthHash $authHash)
	{
		return file_exists(DIR_FS_CATALOG . 'cache/hub_' . $authHash->asString());
	}


	/**
	 * Stores the Hub session key.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash      $authHash
	 * @param \HubPublic\ValueObjects\HubSessionKey $sessionKey
	 */
	private function _storeSessionKey(AuthHash $authHash, HubSessionKey $sessionKey)
	{
		file_put_contents(DIR_FS_CATALOG . 'cache/hub_' . $authHash->asString(), $sessionKey->asString());
	}


	/**
	 * Validates the IP.
	 *
	 * @param array $ipList
	 */
	private function _isIpValid(array $ipList)
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

		$ipWhitelistConfigurationValue = $this->_getIpWhitelist();

		// Check with IP whitelist (comma separated IP values).
		if (!$valid && !empty($ipWhitelistConfigurationValue))
		{
			$ipWhitelist = explode(',', $ipWhitelistConfigurationValue);

			foreach($ipWhitelist as $ipWhitelistEntry) {
				if(empty($ipWhitelistEntry))
				{
					continue;
				}

				// Will also match partial IP values like "192.168.0".
				if ($ipWhitelistEntry === '*' || strpos($ip, trim($ipWhitelistEntry)) !== false)
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
	 * @return HubCallback
	 */
	private function _validateCallbackRequest()
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

					if($response->getStatusCode() === 200)
					{
						$ipList = @json_decode($response->getBody(), true);
						$this->_isIpValid($ipList);
					}
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
	 *
	 * @throws \Exception If DateInterval addition fails.
	 */
	private function _getIpList($bypassCacheValue = false)
	{
		$response = $this->_getCacheValue('hub-ip-list');

		if($response !== null)
		{
			$response = unserialize($response);
		}

		if($response === null || $bypassCacheValue || !is_object($response))
		{
			$response = $this->curlRequest->setUrl($this->_getIpListUrl())->execute();

			if($response->getStatusCode() === 200)
			{
				// serialize value for compatibility with cache of old connector
				$this->_setCacheValue('hub-ip-list', serialize($response), new DateTime('tomorrow'));
			}
		}

		return $response;
	}


	/**
	 * Returns cache value identified by given key.
	 *
	 * @return mixed
	 */
	private function _getCacheValue($key)
	{
		$value = null;

		$cacheFilePath = $this->_getCacheFilePath($key);

		if(file_exists($cacheFilePath))
		{
			$value = unserialize(file_get_contents($cacheFilePath));
		}

		if(isset($value['__expires']))
		{
			$value = $value['__expires'] > new DateTime ? $value['value'] : null;
		}

		return $value;
	}


	/**
	 * Stores value in cache identified by given key.
	 *
	 * @param string         $key
	 * @param                $value
	 * @param \DateTime|null $expires
	 */
	private function _setCacheValue($key, $value, DateTime $expires = null)
	{
		$cacheFilePath = $this->_getCacheFilePath($key);

		if($expires !== null)
		{
			$value = [
				'__expires' => $expires,
				'value'     => $value
			];
		}

		file_put_contents($cacheFilePath, serialize($value));
	}
    
    
    /**
     * Returns the secure token.
     *
     * @return string
     */
    private static function _getSecureToken()
    {
        static $token;
        
        if ($token !== null) {
            return $token;
        }
        
        $token = md5(mt_rand());
        if (file_exists(DIR_FS_CATALOG . 'config/.env.php')) {
            $env   = include DIR_FS_CATALOG . 'config/.env.php';
            $token = $env['APP_SECURITY_TOKEN'] ?? $token;
        }
    }


	/**
	 * Returns the IP list url.
	 *
	 * @return string
	 */
    private function _getIpListUrl()
    {
        $url = 'https://core-api.gambiohub.com/trust/hub_hosts.json';
        $this->_dbConnect();
        if ($this->isLegacyConfiguration) {
            $result = $this->_query('SELECT `configuration_value` FROM `configuration` WHERE `configuration_key` = "MODULE_PAYMENT_GAMBIO_HUB_IP_LIST_URL"');
            if (mysqli_num_rows($result)) {
                $url = mysqli_fetch_assoc($result)['configuration_value'];
            }
        } else {
            $result = $this->_query('SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/MODULE_PAYMENT_GAMBIO_HUB_IP_LIST_URL"');
            if (mysqli_num_rows($result)) {
                $url = mysqli_fetch_assoc($result)['value'];
            }
        }

        return $url;
    }

	/**
	 * Returns the IP whitelist.
	 *
	 * @return string
	 */
    private function _getIpWhitelist()
    {
        $whitelist = '';
        $this->_dbConnect();
        if ($this->isLegacyConfiguration) {
            $result = $this->_query('SELECT `configuration_value` FROM `configuration` WHERE `configuration_key` = "MODULE_PAYMENT_GAMBIO_HUB_IP_WHITELIST"');
            if (mysqli_num_rows($result)) {
                $whitelist = mysqli_fetch_assoc($result)['configuration_value'];
            }
        } else {
            $result = $this->_query('SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/MODULE_PAYMENT_GAMBIO_HUB_IP_WHITELIST"');
            if (mysqli_num_rows($result)) {
                $whitelist = mysqli_fetch_assoc($result)['value'];
            }
        }

        return $whitelist;
    }


	/**
	 * Connects to DB.
	 */
	private function _dbConnect()
	{
		$server   = DB_SERVER;
		$username = DB_SERVER_USERNAME;
		$password = DB_SERVER_PASSWORD;
		$database = DB_DATABASE;

		$port   = isset(explode(':', $server)[1]) && is_numeric(explode(':', $server)[1]) ? (int)explode(':',
		                                                                                                 $server)[1] : null;
		$socket = isset(explode(':', $server)[1]) && !is_numeric(explode(':', $server)[1]) ? explode(':',
		                                                                                             $server)[1] : null;
		$server = explode(':', $server)[0];
        
        mysqli_report(MYSQLI_REPORT_OFF);

		$this->dbLink = mysqli_connect($server, $username, $password, $database, $port, $socket);
        
        // some server configurations only support the port within the host
        if ($this->dbLink === false && $port !== null) {
            $this->dbLink = mysqli_connect(DB_SERVER, $username, $password, $database);
        }

		@mysqli_query($this->dbLink, "SET SESSION sql_mode=''");
		@mysqli_query($this->dbLink, 'SET SQL_BIG_SELECTS=1');
		mysqli_select_db($this->dbLink, $database);
		mysqli_set_charset($this->dbLink, 'utf8');
	}


	/**
	 * Executes mysql query.
	 */
	private function _query($query)
	{
		return mysqli_query($this->_getDbLink(), $query);
	}


	/**
	 * Escapes string for use in mysql query.
	 *
	 * @return string
	 */
	private function _realEscapeString($string)
	{
		if($this->dbLink === null)
		{
			$this->_dbConnect();
		}

		return mysqli_real_escape_string($this->dbLink, $string);
	}


	/**
	 * Returns the DB link.
	 *
	 * @return mysqli
	 */
	private function _getDbLink()
	{
		if($this->dbLink === null)
		{
			$this->_dbConnect();
		}

		return $this->dbLink;
	}


	/**
	 * Returns cache file path.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function _getCacheFilePath($key)
	{
		$cacheFilePath = DIR_FS_CATALOG . 'cache/' . $key . '-persistent_data_cache-' . $this->_getSecureToken()
		                 . '.pdc';

		return $cacheFilePath;
	}


	/**
	 * Logs fallback case for updating the status of an order.
	 *
	 * @param $orderId
	 * @param $orderStatusId
	 * @param $validOrderStatusId
	 */
	private function _logOrderUpdateStatusFallback($orderId, $orderStatusId, $validOrderStatusId)
	{
        $message = "Unknown order status ID $orderStatusId for updating order $orderId."
                   . " Used fallback order status ID $validOrderStatusId instead.";
        $this->_log($message);
	}
    
    
    private function _log(string $message): void
    {
        $log = "================================================================================\n";
        $log .= date('Y-m-d H:i:s') . " {$message}.\n";
        
        file_put_contents(DIR_FS_CATALOG . 'logfiles/hub-' . $this->_getSecureToken() . '.log', $log, FILE_APPEND);
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
            @unlink($cacheFile);
        }
    }
}
