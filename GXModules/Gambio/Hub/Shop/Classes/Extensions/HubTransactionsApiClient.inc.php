<?php
/* --------------------------------------------------------------
   HubTransactionsApiClient.inc.php 2022-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;
use \HubPublic\Serializers\CartContentSerializer;
use \HubPublic\Serializers\ClientSessionInformationSerializer;
use \HubPublic\Serializers\CustomerInformationSerializer;
use \HubPublic\Serializers\HubClientInformationSerializer;
use \HubPublic\Serializers\OrderContentSerializer;
use \HubPublic\ValueObjects\CartContent;
use \HubPublic\ValueObjects\ClientSessionInformation;
use \HubPublic\ValueObjects\CustomerInformation;
use \HubPublic\ValueObjects\HubClientInformation;
use \HubPublic\ValueObjects\HubSessionKey;
use \HubPublic\ValueObjects\HubTransactionCode;
use \HubPublic\ValueObjects\OrderContent;

/**
 * Class HubTransactionsApiClient
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
class HubTransactionsApiClient implements HubTransactionsApiClientInterface
{
	/**
	 * URL to hub endpoint.
	 *
	 * @var string
	 */
	protected $url;
	
	/**
	 * Provide a session key for the transactions.
	 *
	 * @var \HubPublic\ValueObjects\HubSessionKey
	 */
	protected $sessionKey;
	
	/**
	 * cURL request class.
	 *
	 * @var \HubPublic\Http\CurlRequest
	 */
	protected $request;
	
	/**
	 * Cart content serializer.
	 *
	 * @var \HubPublic\Serializers\CartContentSerializer
	 */
	protected $cartContentSerializer;
	
	/**
	 * Customer information serializer.
	 *
	 * @var \HubPublic\Serializers\CustomerInformationSerializer
	 */
	protected $customerInformationSerializer;
	
	/**
	 * Hub client information serializer.
	 *
	 * @var \HubPublic\Serializers\HubClientInformationSerializer
	 */
	protected $hubClientInformationSerializer;
	
	/**
	 * Session information serializer.
	 *
	 * @var \HubPublic\Serializers\ClientSessionInformationSerializer
	 */
	protected $clientSessionInformationSerializer;
	
	/**
	 * Order content serializer.
	 *
	 * @var \HubPublic\Serializers\OrderContentSerializer
	 */
	protected $orderContentSerializer;
	
	/**
	 * Shop logger instance.
	 *
	 * @var \LogControl
	 */
	protected $logControl;
	
	/**
	 * Hub settings
	 *
	 * @var \HubSettings
	 */
	protected $hubSettings;
	
	
	/**
	 * HubTransactionsApiClient constructor.
	 *
	 * @param string                                                    $url                                URL to
	 *                                                                                                      hub
	 *                                                                                                      endpoint.
	 * @param \HubPublic\ValueObjects\HubSessionKey                     $sessionKey                         An active
	 *                                                                                                      Gambio
	 *                                                                                                      Hub
	 *                                                                                                      session
	 *                                                                                                      key.
	 * @param \HubPublic\Http\CurlRequest                               $request                            cURL
	 *                                                                                                      request
	 *                                                                                                      class.
	 * @param \HubPublic\Serializers\CartContentSerializer              $cartContentSerializer              Cart
	 *                                                                                                      content
	 *                                                                                                      serializer.
	 * @param \HubPublic\Serializers\CustomerInformationSerializer      $customerInformationSerializer      Customer
	 *                                                                                                      information
	 *                                                                                                      serializer.
	 * @param \HubPublic\Serializers\HubClientInformationSerializer     $hubClientInformationSerializer     Hub
	 *                                                                                                      client
	 *                                                                                                      information
	 *                                                                                                      serializer.
	 * @param \HubPublic\Serializers\ClientSessionInformationSerializer $clientSessionInformationSerializer Session
	 *                                                                                                      information
	 *                                                                                                      serializer.
	 * @param \HubPublic\Serializers\OrderContentSerializer             $orderContentSerializer             Order
	 *                                                                                                      content
	 *                                                                                                      serializer.
	 * @param \LogControl                                               $logControl                         Log
	 *                                                                                                      communication
	 *                                                                                                      error
	 *                                                                                                      information.
	 * @param \HubSettings                                              $hubSettings                        Hub
	 *                                                                                                      settings.
	 *
	 * @throws InvalidArgumentException If the $url argument is not a valid URL.
	 */
	public function __construct($url,
	                            HubSessionKey $sessionKey,
	                            CurlRequest $request,
	                            CartContentSerializer $cartContentSerializer,
	                            CustomerInformationSerializer $customerInformationSerializer,
	                            HubClientInformationSerializer $hubClientInformationSerializer,
	                            ClientSessionInformationSerializer $clientSessionInformationSerializer,
	                            OrderContentSerializer $orderContentSerializer,
	                            LogControl $logControl,
	                            HubSettings $hubSettings)
	{
		if(!filter_var($url, FILTER_VALIDATE_URL))
		{
			throw new InvalidArgumentException('Invalid Gambio Hub API URL provided: ' . $url);
		}
		
		// Set properties.
		$this->url                                = (string)$url;
		$this->sessionKey                         = $sessionKey;
		$this->request                            = $request;
		$this->cartContentSerializer              = $cartContentSerializer;
		$this->customerInformationSerializer      = $customerInformationSerializer;
		$this->hubClientInformationSerializer     = $hubClientInformationSerializer;
		$this->clientSessionInformationSerializer = $clientSessionInformationSerializer;
		$this->orderContentSerializer             = $orderContentSerializer;
		$this->logControl                         = $logControl;
		$this->hubSettings                        = $hubSettings;
	}
	
	
	/**
	 * Returns an array of allowed payment modules for the respective client.
	 *
	 * @param \HubPublic\ValueObjects\CartContent              $cartContent              Cart content.
	 * @param \HubPublic\ValueObjects\CustomerInformation      $customerInformation      Customer information.
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 * @param array                                            $unallowedModuleCodes     Array of unallowed module
	 *                                                                                   codes.
	 *
	 * @return array Returns an array with the available modules information.
	 *
	 * @throws UnexpectedValueException If Hub returns an error response.
	 */
	public function getAllowedPaymentModules(CartContent $cartContent,
	                                         CustomerInformation $customerInformation,
	                                         HubClientInformation $hubClientInformation,
	                                         ClientSessionInformation $clientSessionInformation,
	                                         array $unallowedModuleCodes)
	{
		// URL for parameter request.
		$parameterRequestUrl = $this->url . '/sessions/' . $this->sessionKey->asString()
		                       . '/allowed_payment_modules/parameters';
		
		// Data array for parameter request.
		$data = [
			'client'               => $this->hubClientInformationSerializer->serialize($hubClientInformation, false),
			'session'              => $this->clientSessionInformationSerializer->serialize($clientSessionInformation,
			                                                                               false),
			'cart'                 => $this->cartContentSerializer->serialize($cartContent, false),
			'customer'             => $this->customerInformationSerializer->serialize($customerInformation, false),
			'unallowedModuleCodes' => $unallowedModuleCodes,
            'additionalParameters' => [],
		];
		
		if(isset($_SESSION['PayPal2Hub']))
        {
            $data['additionalParameters']['PayPal2Hub'] = $_SESSION['PayPal2Hub'];
        }
		
		$json = json_encode($data);
		
		$cacheKey = md5($parameterRequestUrl . $json);
		
		// check if allowed payment modules are already cached
		if(isset($_SESSION['gambio_hub_allowed_payment_modules'][$cacheKey]))
		{
			return $_SESSION['gambio_hub_allowed_payment_modules'][$cacheKey];
		}
		
		// Set parameter request URL.
		$this->request->setUrl($parameterRequestUrl);
		
		// Set parameter request method to 'PUT'.
		$this->request->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
		
		// Set parameter request data.
		$this->request->setOption(CURLOPT_POSTFIELDS, $json);
		
		// Set parameter request options for reading the headers.
		$this->request->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->request->setOption(CURLOPT_HEADER, true);
		$this->request->setOption(CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json)
		]);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            // Perform parameter request.
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to update hub parameters (allowed payment modules). '
                                      . "Could not execute curl request $parameterRequestUrl. Error: "
                                      . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to update hub parameters (allowed payment modules). '
                                               . "Could not execute curl request $parameterRequestUrl. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		// Make sure that there was not error.
		if($httpResponse->getStatusCode() !== 302)
		{
			$this->logControl->notice('Failed to update hub parameters (allowed payment modules). '
			                          . $httpResponse->getBody(), '', 'debug', 'notice', 'USER NOTICE',
			                          $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to update hub parameters (allowed payment modules). '
			                                   . $httpResponse->getBody() . ' (' . $httpResponse->getStatusCode()
			                                   . ')');
		}
		
		// write allowed payment modules cache
		$_SESSION['gambio_hub_allowed_payment_modules'] = [$cacheKey => json_decode($httpResponse->getBody(), true)];
		
		return $_SESSION['gambio_hub_allowed_payment_modules'][$cacheKey];
	}
	
	
	/**
	 * Starts an transaction and returns the transaction code.
	 *
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\OrderContent             $orderContent             Order content.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 *
	 * @return string Return the transaction code.
	 *
	 * @throws UnexpectedValueException If Hub returns an error response.
	 */
	public function startTransaction(HubClientInformation $hubClientInformation,
	                                 OrderContent $orderContent,
	                                 ClientSessionInformation $clientSessionInformation)
	{
		// URL for parameter request.
		$parameterRequestUrl = $this->url . '/sessions/' . $this->sessionKey->asString() . '/transactions/parameters';
		
		// URL for transaction code request.
		$getCodeRequestUrl = $this->url . '/sessions/' . $this->sessionKey->asString() . '/transactions/code';
		
		// Data array for parameter request.
		$data = [
			'order'   => $this->orderContentSerializer->serialize($orderContent, false),
			'client'  => $this->hubClientInformationSerializer->serialize($hubClientInformation, false),
			'session' => $this->clientSessionInformationSerializer->serialize($clientSessionInformation, false),
		];
		
		$json = json_encode($data);
		
		// Set parameter request URL.
		$this->request->setUrl($parameterRequestUrl);
		
		// Set parameter request method to 'PUT'.
		$this->request->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
		
		// Set parameter request data.
		$this->request->setOption(CURLOPT_POSTFIELDS, $json);
		
		// Set required headers.
		$this->request->setOption(CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json)
		]);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            // Perform parameter request.
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to update hub parameters (start transaction). '
                                      . "Could not execute curl request $parameterRequestUrl. Error: "
                                      . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to update hub parameters (start transaction). '
                                               . "Could not execute curl request $parameterRequestUrl. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() !== 302)
		{
			$this->logControl->notice('Failed to update hub parameters (start transaction). '
			                          . $httpResponse->getBody(), '', 'hub', 'notice', 'USER NOTICE',
			                          $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to update hub parameters (start transaction). '
			                                   . $httpResponse->getBody() . ' (' . $httpResponse->getStatusCode()
			                                   . ')');
		}
		
		// Set transaction code request URL.
		$this->request->setUrl($getCodeRequestUrl);
		
		// Expect server response from transaction code request.
		$this->request->setOption(CURLOPT_RETURNTRANSFER, true);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to retrieve transaction code. '
                                      . "Could not execute curl request $getCodeRequestUrl. Error: " . $e->getMessage()
                                      . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to retrieve transaction code. '
                                               . "Could not execute curl request $getCodeRequestUrl. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() !== 200)
		{
			$this->logControl->notice('Failed to retrieve transaction code. ' . $httpResponse->getBody(), '', 'hub',
			                          'notice', 'USER NOTICE', $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to retrieve transaction code. ' . $httpResponse->getBody() . ' ('
			                                   . $httpResponse->getStatusCode() . ')');
		}
		
		// Decoded transaction code JSON response.
		$decodedResponse = json_decode($httpResponse->getBody(), true);
		
		return $decodedResponse['transactionCode'];
	}
	
	
	/**
	 * Returns the transaction details.
	 *
	 * @param \HubPublic\ValueObjects\HubTransactionCode $transactionCode Transaction code.
	 *
	 * @return array Returns the transaction details.
	 *
	 * @throws UnexpectedValueException If Hub returns an error response.
	 */
	public function getTransactionDetails(HubTransactionCode $transactionCode)
	{
		// Request URL.
		$url = $this->url . '/sessions/' . $this->sessionKey->asString() . '/transactions/'
		       . $transactionCode->asString() . '/details';
		
		// Set request URL.
		$this->request->setUrl($url);
		
		// Expect server response from request.
		$this->request->setOption(CURLOPT_RETURNTRANSFER, true);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to retrieve transaction details. '
                                      . "Could not execute curl request $url. Error: " . $e->getMessage()
                                      . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to retrieve transaction details. '
                                               . "Could not execute curl request $url. Error: " . $e->getMessage()
                                               . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                              true));
        }
		
		if($httpResponse->getStatusCode() !== 200)
		{
			$this->logControl->notice('Failed to retrieve transaction details. ' . $httpResponse->getBody(), '', 'hub',
			                          'notice', 'USER NOTICE', $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to retrieve transaction details. ' . $httpResponse->getBody()
			                                   . ' (' . $httpResponse->getStatusCode() . ')');
		}
		
		return $decodedResponse = json_decode($httpResponse->getBody(), true);
	}
	
	
	/**
	 * Returns a string containing html or nothing if payment module has no extra page before confirmation.
	 *
	 * @param \HubPublic\ValueObjects\CartContent              $cartContent              Cart content.
	 * @param \HubPublic\ValueObjects\CustomerInformation      $customerInformation      Customer information.
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 * @param array                                            $getData                  GET-Request data.
	 * @param array                                            $postData                 POST-Request data.
	 * @param string                                           $moduleCode               Module Code.
	 *
	 * @return string Returns a string containing html or nothing.
	 *
	 * @throws UnexpectedValueException If Hub returns an error response.
	 */
	public function getBeforeTransactionPageContent(CartContent $cartContent,
	                                                CustomerInformation $customerInformation,
	                                                HubClientInformation $hubClientInformation,
	                                                ClientSessionInformation $clientSessionInformation,
	                                                array $getData,
	                                                array $postData,
	                                                $moduleCode)
	{
		// URL for parameter request.
		$parameterRequestUrl = $this->url . '/sessions/' . $this->sessionKey->asString()
		                       . '/before_transaction_page/parameters';
		
		// Data array for parameter request.
		$data = [
			'client'             => $this->hubClientInformationSerializer->serialize($hubClientInformation, false),
			'session'            => $this->clientSessionInformationSerializer->serialize($clientSessionInformation,
			                                                                             false),
			'cart'               => $this->cartContentSerializer->serialize($cartContent, false),
			'customer'           => $this->customerInformationSerializer->serialize($customerInformation, false),
			'requestInformation' => ['get' => $getData, 'post' => $postData],
			'moduleCode'         => $moduleCode,
		];
		
		$json = json_encode($data);
		
		// Set parameter request URL.
		$this->request->setUrl($parameterRequestUrl);
		
		// Set parameter request method to 'PUT'.
		$this->request->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
		
		// Set parameter request data.
		$this->request->setOption(CURLOPT_POSTFIELDS, $json);
		
		// Set parameter request options for reading the headers.
		$this->request->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->request->setOption(CURLOPT_HEADER, true);
		$this->request->setOption(CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json)
		]);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
		
        try {
            // Perform parameter request.
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to update Hub parameters (before transaction page). '
                                      . "Could not execute curl request $parameterRequestUrl. Error: "
                                      . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to update Hub parameters (before transaction page). '
                                               . "Could not execute curl request $parameterRequestUrl. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() !== 302)
		{
			$this->logControl->notice('Failed to update Hub parameters (before transaction page). '
			                          . $httpResponse->getBody(), '', 'hub', 'notice', 'USER NOTICE',
			                          $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to update Hub parameters (before transaction page). '
			                                   . $httpResponse->getBody() . ' (' . $httpResponse->getStatusCode()
			                                   . ')');
		}
		
		// Get the response headers as an array.
		$headers = $httpResponse->getHeaders();
		
		// Set list request URL.
		$this->request->setUrl($headers['X-Location']);
		
		// Expect server response from list request.
		$this->request->setOption(CURLOPT_RETURNTRANSFER, true);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to retrieve before transaction page content. '
                                      . "Could not execute curl request {$headers['X-Location']}. Error: "
                                      . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to retrieve before transaction page content. '
                                               . "Could not execute curl request {$headers['X-Location']}. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() !== 200)
		{
			$this->logControl->notice('Failed to retrieve before transaction page content. ' . $httpResponse->getBody(),
			                          '', 'hub', 'notice', 'USER NOTICE', $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to retrieve before transaction page content. '
			                                   . $httpResponse->getBody() . ' (' . $httpResponse->getStatusCode()
			                                   . ')');
		}
		
		return $decodedResponse = json_decode($httpResponse->getBody(), true);
	}
	
	
	/**
	 * Returns an array of confirmation contents served by the selected payment module.
	 *
	 * @param \HubPublic\ValueObjects\CartContent              $cartContent              Cart content.
	 * @param \HubPublic\ValueObjects\CustomerInformation      $customerInformation      Customer information.
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 * @param array                                            $getData                  GET-Request data.
	 * @param array                                            $postData                 POST-Request data.
	 * @param string                                           $moduleCode               Module Code.
	 *
	 * @return string Returns an confirmation contents array.
	 *
	 * @throws UnexpectedValueException If Hub returns an error response.
	 */
	public function getConfirmationContents(CartContent $cartContent,
	                                        CustomerInformation $customerInformation,
	                                        HubClientInformation $hubClientInformation,
	                                        ClientSessionInformation $clientSessionInformation,
	                                        array $getData,
	                                        array $postData,
	                                        $moduleCode)
	{
		// URL for parameter request.
		$parameterRequestUrl = $this->url . '/sessions/' . $this->sessionKey->asString()
		                       . '/confirmation_contents/parameters';
		
		// Data array for parameter request.
		$data = [
			'client'             => $this->hubClientInformationSerializer->serialize($hubClientInformation, false),
			'session'            => $this->clientSessionInformationSerializer->serialize($clientSessionInformation,
			                                                                             false),
			'cart'               => $this->cartContentSerializer->serialize($cartContent, false),
			'customer'           => $this->customerInformationSerializer->serialize($customerInformation, false),
			'requestInformation' => ['get' => $getData, 'post' => $postData],
			'moduleCode'         => $moduleCode,
		];
		
		$json = json_encode($data);
		
		// Set parameter request URL.
		$this->request->setUrl($parameterRequestUrl);
		
		// Set parameter request method to 'PUT'.
		$this->request->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
		
		// Set parameter request data.
		$this->request->setOption(CURLOPT_POSTFIELDS, $json);
		
		// Set parameter request options for reading the headers.
		$this->request->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->request->setOption(CURLOPT_HEADER, true);
		$this->request->setOption(CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json)
		]);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            // Perform parameter request.
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to update Hub parameters (confirmation contents). '
                                      . "Could not execute curl request $parameterRequestUrl. Error: "
                                      . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to update Hub parameters (confirmation contents). '
                                               . "Could not execute curl request $parameterRequestUrl. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() !== 302)
		{
			$this->logControl->notice('Failed to update Hub parameters (confirmation contents). '
			                          . $httpResponse->getBody(), '', 'hub', 'notice', 'USER NOTICE',
			                          $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to update Hub parameters (confirmation contents). '
			                                   . $httpResponse->getBody() . ' (' . $httpResponse->getStatusCode()
			                                   . ')');
		}
		
		// Get the response headers as an array.
		$headers = $httpResponse->getHeaders();
		
		// Set list request URL.
		$this->request->setUrl($headers['X-Location']);
		
		// Expect server response from list request.
		$this->request->setOption(CURLOPT_RETURNTRANSFER, true);
		
		// Set timeout
		$this->request->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            $httpResponse = $this->request->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice('Failed to retrieve confirmation contents. '
                                      . "Could not execute curl request {$headers['X-Location']}. Error: "
                                      . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException('Failed to retrieve confirmation contents. '
                                               . "Could not execute curl request {$headers['X-Location']}. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() !== 200)
		{
			$this->logControl->notice('Failed to retrieve confirmation contents. ' . $httpResponse->getBody(), '',
			                          'hub', 'notice', 'USER NOTICE', $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to retrieve confirmation contents. ' . $httpResponse->getBody()
			                                   . ' (' . $httpResponse->getStatusCode() . ')');
		}
		
		return $decodedResponse = json_decode($httpResponse->getBody(), true);
	}
}
