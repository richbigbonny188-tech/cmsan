<?php
/* --------------------------------------------------------------
   HubCallbackApiClient.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;

/**
 * Class HubCallbackApiClient
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
class HubCallbackApiClient implements HubCallbackApiClientInterface
{
	/**
	 * @var string
	 */
	protected $url;
	
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
	 * HubCallbackApiClient constructor.
	 *
	 * @param string                      $url         Gambio Hub API URL
	 * @param \HubPublic\Http\CurlRequest $curlRequest Make cURL requests to the Hub API.
	 * @param \LogControl                 $logControl  Log communication error information.
	 * @param \HubSettings                $hubSettings Hub settings.
	 *
	 * @throws InvalidArgumentException If the $url argument is not a string or not a valid URL.
	 */
	public function __construct($url,
	                            CurlRequest $curlRequest,
	                            LogControl $logControl,
	                            HubSettings $hubSettings)
	{
		if(!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL))
		{
			throw new InvalidArgumentException('Invalid Gambio Hub API URL provided: ' . $url);
		}
		
		$this->url         = $url;
		$this->curlRequest = $curlRequest;
		$this->logControl  = $logControl;
		$this->hubSettings  = $hubSettings;
	}
	
	
	/**
	 * Executes Gambio Hub payment module callback.
	 *
	 * @param string $paymentModuleCode Gambio Hub Payment Module Code
	 * @param bool   $isPostRequest     Flag, if url will be executed via GET or POST
	 * @param array  $getData           GET data as an array
	 * @param array  $postData          POST data as an array
	 * @param array  $headers           Headers as an array like ['X-Custom-Header: Foo']
	 *
	 * @return \HubPublic\ValueObjects\HttpResponse Returns the HTTP response
	 *
	 * @throws InvalidArgumentException If $paymentModuleCode argument is not a string.
	 * @throws UnexpectedValueException If the server responses with status code different to 201.
	 */
	public function execute($paymentModuleCode,
	                        $isPostRequest = false,
	                        array $getData = [],
	                        array $postData = [],
	                        array $headers = [])
	{
		if(!is_string($paymentModuleCode))
		{
			throw new InvalidArgumentException('Payment module code is not a string (' . gettype($paymentModuleCode)
			                                   . ')');
		}
		
		$requestUrl = $this->url . '/payment_modules/' . rawurlencode($paymentModuleCode) . '/callback';
		
		if(count($getData))
		{
			$requestUrl .= '?' . http_build_query($getData, '', '&', PHP_QUERY_RFC3986);
		}
		
		// Set parameter request URL.
		$this->curlRequest->setUrl($requestUrl);
		
		if(count($headers))
		{
			// Set parameter request data.
			$this->curlRequest->setOption(CURLOPT_HTTPHEADER, $headers);
		}
		
		if($isPostRequest !== false)
		{
			// Set parameter request method to 'POST'.
			$this->curlRequest->setOption(CURLOPT_POST, true);
			
			// Set POST data
			$this->curlRequest->setOption(CURLOPT_POSTFIELDS, $postData);
		}
		
		// Set timeout
		$this->curlRequest->setOption(CURLOPT_TIMEOUT, $this->hubSettings->getCurlTimeout());
        
        try {
            // Perform parameter request.
            $httpResponse = $this->curlRequest->execute();
        } catch (CurlRequestException $e) {
            $this->logControl->notice("Failed to execute callback to url $requestUrl. Error: " . $e->getMessage()
                                      . "\ncurl-Info: " . var_export($e->getCurlInfo(), true),
                                      '',
                                      'hub',
                                      'notice',
                                      'USER NOTICE');
            
            throw new UnexpectedValueException("Failed to execute callback to url $requestUrl. Error: "
                                               . $e->getMessage() . "\ncurl-Info: " . var_export($e->getCurlInfo(),
                                                                                                 true));
        }
		
		if($httpResponse->getStatusCode() >= 400)
		{
			$this->logControl->notice('Failed to execute callback to url ' . $requestUrl . '. Response is: '
			                          . $httpResponse->getBody(), '', 'hub', 'notice', 'USER NOTICE',
			                          $httpResponse->getStatusCode());
			
			throw new UnexpectedValueException('Failed to execute callback to url ' . $requestUrl . '. Response is: '
			                                   . $httpResponse->getBody() . ' (' . $httpResponse->getStatusCode()
			                                   . ')');
		}
		
		return $httpResponse;
	}
}