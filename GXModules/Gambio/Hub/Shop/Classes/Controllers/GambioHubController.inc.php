<?php
/* --------------------------------------------------------------
   GambioHubController.inc.php 2022-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

/**
 * Class GambioHubController
 *
 * This class contains the Hub Web-API callbacks of shop, for versions that include the HttpService. For
 * legacy versions check the request_port.php and the respective AjaxHandler classes.
 */
class GambioHubController extends HttpViewController
{
	/**
	 * Hub callback handler
	 *
	 * @var HubCallbackHandlerInterface
	 */
	protected $hubCallbackHandler;
	
	/**
	 * @var array Server data.
	 */
	protected $serverDataArray;
	
	
	/**
	 * Initializes the controller
	 */
	public function init()
	{
		$this->hubCallbackHandler = MainFactory::create('HubCallbackHandler', MainFactory::create('HubServiceFactory'),
		                                                MainFactory::create('HubClientKeyConfiguration'),
		                                                new CurlRequest(), MODULE_PAYMENT_GAMBIO_HUB_IP_LIST_URL);
	}
	
	
	/**
	 * @param \HttpContextInterface $httpContext
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->serverDataArray = $this->httpContextReader->getServerData($httpContext);
		
		parent::proceed($httpContext);
	}
	
	
	/**
	 * Stores the client key to the shop configuration.
	 */
	public function actionClientKeyCallback()
	{
		$response = $this->hubCallbackHandler->proceedClientKeyCallback($this->_getServerData('HTTP_X_AUTH_HASH'),
		                                                                $this->_getPostData('clientKey'),
		                                                                $this->_getPostData('shopKey'));
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Stores the session key to the shop.
	 */
	public function actionSessionKeyCallback()
	{
		$response = $this->hubCallbackHandler->proceedSessionKeyCallback($this->_getServerData('HTTP_X_AUTH_HASH'),
		                                                                 $this->_getPostData('sessionKey'));
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Updates the status of an existing order.
	 *
	 * This method will set the new status for a specific shop order. Order statuses will be changed
	 * as the payment is processed.
	 *
	 * Provide the "orderId" and "orderStatusId" POST parameters for the status modification and the
	 * "X-Client-Key" header for the authorization.
	 *
	 * @return JsonHttpControllerResponse Returns a success message or a response error.
	 */
	public function actionUpdateOrderStatusCallback()
	{
		$response = $this->hubCallbackHandler->proceedUpdateOrderStatusCallback($this->_getServerData('HTTP_X_CLIENT_KEY'),
		                                                                        $this->_getPostData('orderId'),
		                                                                        $this->_getPostData('orderStatusId'));
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Inserts a new order status name.
	 *
	 * This method will set the new order status name.
	 *
	 * The language code and language name are provided as an array for the new order status name and the
	 * "X-Client-Key" header for the authorization.
	 *
	 * @return JsonHttpControllerResponse Returns a success message or a response error.
	 */
	public function actionCreateOrderStatusCallback()
	{
		$response = $this->hubCallbackHandler->proceedCreateOrderStatusCallback($this->_getServerData('HTTP_X_CLIENT_KEY'),
		                                                                        $this->_getPostData('order_status_name'));
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Inserts a new configuration value for given configuration key.
	 *
	 * @return JsonHttpControllerResponse Returns a success message or a response error.
	 */
	public function actionUpdateConfigurationCallback()
	{
		$response = $this->hubCallbackHandler->proceedUpdateConfiguration($this->_getServerData('HTTP_X_CLIENT_KEY'),
		                                                                  $this->_getPostData('key'),
		                                                                  $this->_getPostData('value'));
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Reads a configuration value for given configuration key.
	 *
	 * @return JsonHttpControllerResponse Returns a success message and the requested configuration value or a response
	 *                                    error.
	 */
	public function actionGetConfigurationCallback()
	{
		$response = $this->hubCallbackHandler->proceedGetConfiguration($this->_getServerData('HTTP_X_CLIENT_KEY'),
		                                                               $this->_getQueryParameter('key'));
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Performs callback GET-request to Gambio Hub API to get redirection URL of payment provider.
	 *
	 * The redirection will be performed immediately. A moduleCode-GET-parameter is mandatory containing the Gambio Hub
	 * Payment Module Code like PayPalHub.
	 */
	public function actionPaymentRedirection()
	{
		// Hub settings instance.
		$hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
		
		$hubCallbackApiClient = MainFactory::create('HubCallbackApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
		                                            new CurlRequest(), LogControl::get_instance(), $hubSettings);
		
		$getData                  = $this->_getQueryParametersCollection()->getArray();
		$getData['language_code'] = $_SESSION['language_code'];
		$getData['client_key']    = gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
		unset($getData['do'], $getData['moduleCode']);
        
        try {
            $httpResponse = $hubCallbackApiClient->execute($_GET['moduleCode'], false, $getData);
        } catch (UnexpectedValueException $e) {
            $logControl = LogControl::get_instance();
            
            $logControl->notice('Payment redirection failed because of missing redirect url. Response is: '
                                . $e->getMessage(),
                                '',
                                'hub',
                                'notice',
                                'USER NOTICE');
            
            return MainFactory::create('RedirectHttpControllerResponse',
                                       HTTP_SERVER . DIR_WS_CATALOG . FILENAME_DEFAULT);
        }
		
		$callbackJson = @json_decode($httpResponse->getBody(), true);
		
		if(!is_array($callbackJson) || !isset($callbackJson['redirectUrl']))
		{
			$logControl = LogControl::get_instance();
			
			$logControl->notice('Payment redirection failed because of missing redirect url. Response is: '
			                    . $httpResponse->getBody(), '', 'hub', 'notice', 'USER NOTICE',
			                    $httpResponse->getStatusCode());
			
			$callbackJson = ['redirectUrl' => HTTP_SERVER . DIR_WS_CATALOG . FILENAME_DEFAULT];
		}
		
		return MainFactory::create('RedirectHttpControllerResponse', $callbackJson['redirectUrl']);
	}
	
	
	/**
	 * Performs callback POST-request to Gambio Hub API to send payment data ($_GET and $_POST) to the hub and get
	 * page content to be displayed.
	 *
	 * A moduleCode-GET-parameter is mandatory containing the Gambio Hub Payment Module Code like PayPalHub.
	 *
	 * @return HttpControllerResponse|RedirectHttpControllerResponse
	 */
	public function actionPaymentPage()
	{
		// Hub settings instance.
		$hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
		
		$hubCallbackApiClient     = MainFactory::create('HubCallbackApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
		                                                new CurlRequest(), LogControl::get_instance(), $hubSettings);
		$getData                  = $this->_getQueryParametersCollection()->getArray();
		$getData['language_code'] = $_SESSION['language_code'];
		$getData['client_key']    = gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
		unset($getData['do'], $getData['moduleCode']);
        
        try {
            $httpResponse = $hubCallbackApiClient->execute($_GET['moduleCode'],
                                                           true,
                                                           $getData,
                                                           $this->_getPostDataCollection()->getArray());
        } catch (UnexpectedValueException $e) {
            $logControl = LogControl::get_instance();
            
            $logControl->notice('Payment page could not be rendered because of missing page content. Response is: '
                                . $e->getMessage(),
                                '',
                                'hub',
                                'notice',
                                'USER NOTICE');
            
            return MainFactory::create('RedirectHttpControllerResponse',
                                       HTTP_SERVER . DIR_WS_CATALOG . FILENAME_DEFAULT);
        }
		
		$callbackJson = @json_decode($httpResponse->getBody(), true);
		
		if(!is_array($callbackJson) || !isset($callbackJson['pageContent']))
		{
			$logControl = LogControl::get_instance();
			
			$logControl->notice('Payment page could not be rendered because of missing page content. Response is: '
			                    . $httpResponse->getBody(), '', 'hub', 'notice', 'USER NOTICE',
			                    $httpResponse->getStatusCode());
			
			return MainFactory::create('RedirectHttpControllerResponse',
			                           HTTP_SERVER . DIR_WS_CATALOG . FILENAME_DEFAULT);
		}
		
		$layoutContentControl = MainFactory::create_object('LayoutContentControl');
		$layoutContentControl->set_data('GET', $this->_getQueryParametersCollection()->getArray());
		$layoutContentControl->set_data('POST', $this->_getPostDataCollection()->getArray());
		$layoutContentControl->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
		$layoutContentControl->set_('coo_product', $GLOBALS['product']);
		$layoutContentControl->set_('coo_xtc_price', $GLOBALS['xtPrice']);
		$layoutContentControl->set_('c_path', $GLOBALS['cPath']);
		$layoutContentControl->set_('main_content', $callbackJson['pageContent']);
		$layoutContentControl->set_('request_type', $GLOBALS['request_type']);
		$layoutContentControl->proceed();
		
		return MainFactory::create('HttpControllerResponse', $layoutContentControl->get_response());
	}
	
	
	/**
	 * Returns the expected $_SERVER value by the given key name.
	 *
	 * This method is the object oriented layer for $_SERVER[$keyName].
	 *
	 * @param string $keyName Expected key of server parameter.
	 *
	 * @return string|null Either the expected value or null, of not found.
	 */
	protected function _getServerData($keyName)
	{
		if(!array_key_exists($keyName, $this->serverDataArray))
		{
			return null;
		}
		
		return $this->serverDataArray[$keyName];
	}
}

