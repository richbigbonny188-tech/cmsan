<?php
/* --------------------------------------------------------------
   KlarnaHubCallbackClient.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;
use \HubPublic\ValueObjects\HubClientKey;

/**
 * Class KlarnaHubCallbackClient
 *
 * Executes requests towards KlarnaHub callback endpoints.
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class KlarnaHubCallbackClient
{
	/**
	 * @var string
	 */
	protected $url;
	
	/**
	 * @var string
	 */
	protected $clientKey;
	
	/**
	 * @var string
	 */
	protected $orderNumber;
	
	
	/**
	 * KlarnaHubCallbackClient constructor.
	 */
	public function __construct(NonEmptyStringType $url, HubClientKey $clientKey, NonEmptyStringType $orderNumber)
	{
		$this->url         = $url->asString();
		$this->clientKey   = $clientKey->asString();
		$this->orderNumber = $orderNumber->asString();
	}
	
	
	/**
	 * Executes the full capture callback for the provided order.
	 *
	 * @param array $fullCaptureData Contains the required callback data (see KlarnaHub callback for further info).
	 *
	 * @throws \HubPublic\Exceptions\CurlRequestException
	 * @throws \HubPublic\Exceptions\InvalidHttpCodeException
	 * @throws \KlarnaHubException If the response contains a Klarna API error response.
	 */
	public function executeFullCapture(array $fullCaptureData)
	{
		$queryParams = $this->_getQueryParams('full-capture');
		
		$url = $this->url . '?' . http_build_query($queryParams, '', '&');
		
		$this->_addDebugMessage('Executing full capture request.',
		                        ['fullCaptureData' => $fullCaptureData, 'url' => $url]);
		
		$curlRequest = new CurlRequest();
		
		$curlRequest->setUrl($url)
		            ->setOption(CURLOPT_POST, true)
		            ->setOption(CURLOPT_POSTFIELDS, json_encode($fullCaptureData))
		            ->setOption(CURLOPT_RETURNTRANSFER, true)
		            ->setOption(CURLOPT_HTTPHEADER, [
			            'Content-Type: application/json'
		            ]);
		
		$httpResponse = $curlRequest->execute();
		
		$this->_addDebugMessage('Got response for full capture request.', ['httpResponse' => $httpResponse]);
		
		if($httpResponse->getStatusCode() !== 204)
		{
			$errorResponse = json_decode($httpResponse->getBody(), true);
			
			if(is_array($errorResponse) && array_key_exists('error_code', $errorResponse)
			   && array_key_exists('error_messages', $errorResponse))
			{
				throw new KlarnaHubException($errorResponse);
			}
			
			throw new RuntimeException('Response contains invalid status code: ' . $httpResponse->getStatusCode());
		}
	}
	
	
	/**
	 * Executes the capture callback for the provided order.
	 *
	 * @param array $captureData Contains the required callback data (see KlarnaHub callback for further info).
	 *
	 * @throws \HubPublic\Exceptions\CurlRequestException
	 * @throws \HubPublic\Exceptions\InvalidHttpCodeException
	 * @throws \KlarnaHubException If the response contains a Klarna API error response.
	 */
	public function executeCapture(array $captureData)
	{
		$queryParams = $this->_getQueryParams('capture');
		
		$url = $this->url . '?' . http_build_query($queryParams, '', '&');
		
		$this->_addDebugMessage('Executing capture request.', ['captureData' => $captureData, 'url' => $url]);
		
		$curlRequest = new CurlRequest();
		
		$curlRequest->setUrl($url)
		            ->setOption(CURLOPT_POST, true)
		            ->setOption(CURLOPT_POSTFIELDS, json_encode($captureData))
		            ->setOption(CURLOPT_RETURNTRANSFER, true)
		            ->setOption(CURLOPT_HTTPHEADER, [
			            'Content-Type: application/json'
		            ]);
		
		$httpResponse = $curlRequest->execute();
		
		$this->_addDebugMessage('Got response for capture request.', ['httpResponse' => $httpResponse]);
		
		if($httpResponse->getStatusCode() !== 204)
		{
			$errorResponse = json_decode($httpResponse->getBody(), true);
			
			if(is_array($errorResponse) && array_key_exists('error_code', $errorResponse)
			   && array_key_exists('error_messages', $errorResponse))
			{
				throw new KlarnaHubException($errorResponse);
			}
			
			throw new RuntimeException('Response contains invalid status code: ' . $httpResponse->getStatusCode());
		}
	}
	
	
	/**
	 * Executes the reduce invoice amount callback for the provided order.
	 *
	 * @param array $reduceInvoiceAmountData Contains the required callback data (see KlarnaHub callback for further
	 *                                       info).
	 *
	 * @throws \HubPublic\Exceptions\CurlRequestException
	 * @throws \HubPublic\Exceptions\InvalidHttpCodeException
	 * @throws \KlarnaHubException If the response contains a Klarna API error response.
	 */
	public function executeReduceInvoiceAmount(array $reduceInvoiceAmountData)
	{
		$queryParams = $this->_getQueryParams('reduce-amount');
		
		$url = $this->url . '?' . http_build_query($queryParams, '', '&');
		
		$this->_addDebugMessage('Executing reduce invoice amount request.',
		                        ['reduceInvoiceAmountData' => $reduceInvoiceAmountData, 'url' => $url]);
		
		$curlRequest = new CurlRequest();
		
		$curlRequest->setUrl($url)
		            ->setOption(CURLOPT_POST, true)
		            ->setOption(CURLOPT_POSTFIELDS, json_encode($reduceInvoiceAmountData))
		            ->setOption(CURLOPT_RETURNTRANSFER, true)
		            ->setOption(CURLOPT_HTTPHEADER, [
			            'Content-Type: application/json'
		            ]);
		
		$httpResponse = $curlRequest->execute();
		
		$this->_addDebugMessage('Got response for reduce invoice amount request.', ['httpResponse' => $httpResponse]);
		
		if($httpResponse->getStatusCode() !== 204)
		{
			// Do not throw an exception if this is a Klarna error and thus is recorded.
			$errorResponse = json_decode($httpResponse->getBody(), true);
			
			if(is_array($errorResponse) && array_key_exists('error_code', $errorResponse)
			   && array_key_exists('error_messages', $errorResponse))
			{
				throw new KlarnaHubException($errorResponse);
			}
			
			throw new RuntimeException('Response contains invalid status code: ' . $httpResponse->getStatusCode());
		}
	}
	
	
	/**
	 * Executes the add tracking code callback for the provided order.
	 *
	 * @param array $addTrackingCodeData     Contains the required callback data (see KlarnaHub callback for further
	 *                                       info).
	 *
	 * @throws \HubPublic\Exceptions\CurlRequestException
	 * @throws \HubPublic\Exceptions\InvalidHttpCodeException
	 * @throws \KlarnaHubException If the response contains a Klarna API error response.
	 */
	public function executeAddTrackingCode(array $addTrackingCodeData)
	{
		$queryParams = $this->_getQueryParams('add-tracking-code');
		
		$url = $this->url . '?' . http_build_query($queryParams, '', '&');
		
		$this->_addDebugMessage('Executing add tracking code request.',
		                        ['addTrackingCodeData' => $addTrackingCodeData, 'url' => $url]);
		
		$curlRequest = new CurlRequest();
		
		$curlRequest->setUrl($url)
		            ->setOption(CURLOPT_POST, true)
		            ->setOption(CURLOPT_POSTFIELDS, json_encode($addTrackingCodeData))
		            ->setOption(CURLOPT_RETURNTRANSFER, true)
		            ->setOption(CURLOPT_HTTPHEADER, [
			            'Content-Type: application/json'
		            ]);
		
		$httpResponse = $curlRequest->execute();
		
		$this->_addDebugMessage('Got response for add tracking code request.', ['httpResponse' => $httpResponse]);
		
		if($httpResponse->getStatusCode() !== 204)
		{
			// Do not throw an exception if this is a Klarna error and thus is recorded.
			$errorResponse = json_decode($httpResponse->getBody(), true);
			
			if(is_array($errorResponse) && array_key_exists('error_code', $errorResponse)
			   && array_key_exists('error_messages', $errorResponse))
			{
				throw new KlarnaHubException($errorResponse);
			}
			
			throw new RuntimeException('Response contains invalid status code: ' . $httpResponse->getStatusCode());
		}
	}
	
	
	/**
	 * Executes the cancel order callback for hte provided order.
	 *
	 * @throws \HubPublic\Exceptions\CurlRequestException
	 * @throws \HubPublic\Exceptions\InvalidHttpCodeException
	 * @throws \KlarnaHubException If the response contains a Klarna API error response.
	 */
	public function executeCancelOrder()
	{
		$queryParams = $this->_getQueryParams('cancel-order');
		
		$url = $this->url . '?' . http_build_query($queryParams, '', '&');
		
		$this->_addDebugMessage('Executing cancel order request.', ['cancelOrder' => [], 'url' => $url]);
		
		$curlRequest = new CurlRequest();
		
		$curlRequest->setUrl($url)
		            ->setOption(CURLOPT_POST, true)
		            ->setOption(CURLOPT_POSTFIELDS, json_encode([]))
		            ->setOption(CURLOPT_RETURNTRANSFER, true)
		            ->setOption(CURLOPT_HTTPHEADER, [
			            'Content-Type: application/json'
		            ]);
		
		$httpResponse = $curlRequest->execute();
		
		$this->_addDebugMessage('Got response for cancel order request.', ['httpResponse' => $httpResponse]);
		
		if($httpResponse->getStatusCode() !== 204)
		{
			// Do not throw an exception if this is a Klarna error and thus is recorded.
			$errorResponse = json_decode($httpResponse->getBody(), true);
			
			if(is_array($errorResponse) && array_key_exists('error_code', $errorResponse)
			   && array_key_exists('error_messages', $errorResponse))
			{
				throw new KlarnaHubException($errorResponse);
			}
			
			throw new RuntimeException('Response contains invalid status code: ' . $httpResponse->getStatusCode());
		}
	}
	
	
	/**
	 * Returns a Klarna order object.
	 *
	 * @return array
	 *
	 * @throws \HubPublic\Exceptions\CurlRequestException
	 * @throws \HubPublic\Exceptions\InvalidHttpCodeException
	 * @throws \KlarnaHubException If the response contains a Klarna API error response.
	 */
	public function getKlarnaOrder()
	{
		$queryParams = $this->_getQueryParams('get-order');
		
		$url = $this->url . '?' . http_build_query($queryParams, '', '&');
		
		$this->_addDebugMessage('Executing get Klarna order request.', ['url' => $url]);
		
		$curlRequest = new CurlRequest();
		
		$curlRequest->setUrl($url)->setOption(CURLOPT_RETURNTRANSFER, true);
		
		$httpResponse = $curlRequest->execute();
		
		$this->_addDebugMessage('Got response for get Klarna order request.', ['httpResponse' => $httpResponse]);
		
		if($httpResponse->getStatusCode() !== 200)
		{
			// Do not throw an exception if this is a Klarna error and thus is recorded.
			$errorResponse = json_decode($httpResponse->getBody(), true);
			
			if(is_array($errorResponse) && array_key_exists('error_code', $errorResponse)
			   && array_key_exists('error_messages', $errorResponse))
			{
				throw new KlarnaHubException($errorResponse);
			}
			
			throw new RuntimeException('Response contains invalid status code: ' . $httpResponse->getStatusCode());
		}
		
		return json_decode($httpResponse->getBody(), true);
	}
	
	
	/**
	 * Returns the query parameters as an associative array.
	 *
	 * @param string $action Provide a valid action name.
	 *
	 * @return array
	 */
	protected function _getQueryParams($action)
	{
		return [
			'source'       => 'shop',
			'action'       => $action,
			'client_key'   => $this->clientKey,
			'order_number' => $this->orderNumber
		];
	}
	
	
	/**
	 * Adds a debug message if shop logging is enabled.
	 *
	 * @param string $message Debug message to be logged.
	 * @param array  $context Context values important for debugging.
	 */
	protected function _addDebugMessage($message, array $context = [])
	{
		if(!filter_var(gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_KLARNAHUB_DEBUGLOGGING'), FILTER_VALIDATE_BOOLEAN))
		{
			return;
		}
		
		LogControl::get_instance()->notice($message . PHP_EOL . 'Context: ' . var_export($context, true) . PHP_EOL, '',
		                                   'klarnahub');
	}
}