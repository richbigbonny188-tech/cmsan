<?php
/* --------------------------------------------------------------
	PayPalRestService.inc.php 2018-10-18
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * PayPal-specific subclass of RestService.
 * Implements logging and timeout.
 */
class PayPalRestService extends RestService
{
	/**
	 * @var PayPalLogger logging facility
	 */
	protected $logger;
	
	/**
	 * @var bool
	 */
	protected $logRawResponseEnabled = true;
	
	/**
	 * @var bool
	 */
	protected $logDecodedResponseEnabled = true;

	/**
	 * @var RestRequest last request (stored for logging/debugging)
	 */
	protected $lastRequest;

	/**
	 * @var PayPalRestResponse last response
	 */
	protected $lastResponse;

	const DEFAULT_API_TIMEOUT = 10;
	
	/**
	 * initializes the service with a default timeout of 10 seconds
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTimeout(self::DEFAULT_API_TIMEOUT);
		$this->logger = MainFactory::create_object('PayPalLogger');
	}
	
	
	/**
	 * performs a request.
	 * Request and response are logged if extended logging is active.
	 *
	 * @param RestRequest $request
	 *
	 * @return PayPalRestResponse
	 * @throws \Exception
	 */
	public function performRequest(RestRequest $request)
	{
		$this->logger->debug_notice("API request:\n".$request);
		$this->lastRequest = $request;
		try
		{
			$restCurlResponse = parent::performRequest($request);
			if($this->isLogRawResponseEnabled())
			{
				$this->logger->debug_notice("API response:\n".$restCurlResponse);
			}
			$paypalRestResponse = MainFactory::create_object('PayPalRestResponse', array($restCurlResponse));
			if($this->isLogDecodedResponseEnabled())
			{
				$this->logger->debug_notice("API response decoded:\n".print_r($paypalRestResponse->getResponseObject(), true));
			}
			$this->lastResponse = $paypalRestResponse;
			return $paypalRestResponse;
		}
		catch(Exception $e)
		{
			$this->logger->debug_notice('ERROR performing request: '.$e->getMessage());
			throw $e;
		}
	}

	/**
	 * returns the last request
	 * @return RestRequest last request tried by performRequest()
	 */
	public function getLastRequest()
	{
		return $this->lastRequest;
	}

	/**
	 * returns last response
	 * @return PayPalRestResponse
	 */
	public function getLastResponse()
	{
		return $this->lastResponse;
	}
	
	
	/**
	 * @return bool
	 */
	public function isLogRawResponseEnabled()
	{
		return $this->logRawResponseEnabled;
	}
	
	
	/**
	 * @param bool $logRawResponseEnabled
	 */
	public function setLogRawResponseEnabled($logRawResponseEnabled)
	{
		$this->logRawResponseEnabled = $logRawResponseEnabled;
	}
	
	
	/**
	 * @return bool
	 */
	public function isLogDecodedResponseEnabled()
	{
		return $this->logDecodedResponseEnabled;
	}
	
	
	/**
	 * @param bool $logDecodedResponseEnabled
	 */
	public function setLogDecodedResponseEnabled($logDecodedResponseEnabled)
	{
		$this->logDecodedResponseEnabled = $logDecodedResponseEnabled;
	}
	
}
