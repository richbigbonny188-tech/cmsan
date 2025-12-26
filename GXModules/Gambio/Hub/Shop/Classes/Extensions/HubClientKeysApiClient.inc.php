<?php
/* --------------------------------------------------------------
   HubClientKeysApiClient.inc.php 2022-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;
use \HubPublic\ValueObjects\AuthHash;
use \HubPublic\ValueObjects\HubClientKey;

/**
 * Class HubClientKeysApiClient
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
class HubClientKeysApiClient implements HubClientKeysApiClientInterface
{
	/**
	 * @var string
	 */
	protected $url;
	
	/**
	 * @var \HubShopKeyConfigurationInterface
	 */
	protected $shopKeyConfiguration;
	
	/**
	 * @var \HubClientKeyConfigurationInterface
	 */
	protected $hubClientKeyConfiguration;
	
	/**
	 * @var \HubPublic\Http\CurlRequest
	 */
	protected $curlRequest;
	
	/**
	 * @var \LogControl
	 */
	protected $logControl;
	
	/**
	 * @var \HubSettings
	 */
	protected $hubSettings;
	
	
	/**
	 * HubClientKeysApiClient constructor.
	 *
	 * @param string                              $url                               Gambio Hub API URL
	 * @param \HubShopKeyConfigurationInterface   $shopKeyConfiguration              Retrieves the shop key from shop
	 * @param \HubClientKeyConfigurationInterface $hubClientKeyConfiguration         Stores or retrieves the hub
	 *                                                                               client key from shop
	 *                                                                               configuration.
	 * @param \HubPublic\Http\CurlRequest         $curlRequest                       Make cURL requests to the Hub
	 *                                                                               API.
	 * @param \LogControl                         $logControl                        Log communication error
	 *                                                                               information.
	 * @param \HubSettings                        $hubSettings                       Hub settings.
	 *
	 * @throws InvalidArgumentException If the $url argument is not a valid URL.
	 */
	public function __construct($url,
	                            HubShopKeyConfigurationInterface $shopKeyConfiguration,
	                            HubClientKeyConfigurationInterface $hubClientKeyConfiguration,
	                            CurlRequest $curlRequest,
	                            LogControl $logControl,
	                            HubSettings $hubSettings)
	{
		if(!filter_var($url, FILTER_VALIDATE_URL))
		{
			throw new InvalidArgumentException('Invalid Gambio Hub API URL provided: ' . $url);
		}
		
		$this->url                       = $url;
		$this->shopKeyConfiguration      = $shopKeyConfiguration;
		$this->hubClientKeyConfiguration = $hubClientKeyConfiguration;
		$this->curlRequest               = $curlRequest;
		$this->logControl                = $logControl;
		$this->hubSettings               = $hubSettings;
	}
	
	
	/**
	 * Creates a client key in the Gambio Hub.
	 *
	 * Provide an authorization hash that can be later used in the hub callbacks to determine where each
	 * session key belongs to. This method will additionally save the AuthHash value to the PHP session with
	 * the key 'gambio_hub_auth_hash' for later reference.
	 *
	 * @param \HubPublic\ValueObjects\AuthHash $authHash    The authorization hash to be used.
	 * @param string                           $shopUrl     Shop URL (with trailing slash).
	 * @param string                           $shopVersion Current shop version (without leading "v").
	 *
	 * @throws UnexpectedValueException If the server responses with status code different to 201.
	 *
	 * @return \HubPublic\ValueObjects\HubClientKey Returns the new client key.
	 */
	public function createClientKey(AuthHash $authHash, $shopUrl, $shopVersion)
	{
		$shopKey = $this->shopKeyConfiguration->get();
		
		$requestUrl = $this->url . '/shop_keys/' . $shopKey;
		
		// Set parameter request URL.
		$this->curlRequest->setUrl($requestUrl);
		
		// Set parameter request method to 'POST'.
		$this->curlRequest->setOption(CURLOPT_CUSTOMREQUEST, 'POST');
		
		// Set parameter request data. 
		$this->curlRequest->setOption(CURLOPT_POSTFIELDS, [
			'url'     => $shopUrl,
			'version' => $shopVersion
		]);
		
		// Set parameter request data.
		$this->curlRequest->setOption(CURLOPT_HTTPHEADER, ['X-Auth-Hash: ' . $authHash->asString()]);
		
		// Set timeout
		$this->curlRequest->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            // Perform parameter request.
            $httpResponse = $this->curlRequest->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to create a new hub client key. '
                                      . "Could not execute curl request $requestUrl. Error: " . $e->getMessage()
                                      . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to create a new hub client key. '
                                               . "Could not execute curl request $requestUrl. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() !== 201)
		{
			$this->logControl->notice('Failed to create a new hub client key. ' . $httpResponse->getBody(), '', 'hub',
			                          'notice', 'USER NOTICE', $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to create a new hub client key. ' . $httpResponse->getBody()
			                                   . ' (' . $httpResponse->getStatusCode() . ')');
		}
		
		return new HubClientKey($this->hubClientKeyConfiguration->get());
	}
}