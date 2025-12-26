<?php
/* --------------------------------------------------------------
   GambioHubOrderExtender.inc.php 2019-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

/**
 * Class GambioHubOrderExtender
 */
class GambioHubOrderExtender extends GambioHubOrderExtender_parent
{
	/**
	 * @var \HubAssetHelper
	 */
	protected $hubAssetHelper;
	
	/**
	 * Proceed with the execution of the extender.
	 */
	public function proceed()
	{
		parent::proceed();
		$installedVersion = gm_get_conf('INSTALLED_VERSION');
		$this->hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);
		
		$module = $this->_getModuleData();
		
		$this->_addOrderDetailsScript($module)->_addOrderFrontendIframe($module)->addContent();
	}
	
	
	/**
	 * Returns array containing the Hub module "code" and "title" properties.
	 *
	 * @return array
	 */
	protected function _getModuleData()
	{
		$sql   = '
			SELECT 
				`gambio_hub_module` AS `code`, 
				`gambio_hub_module_title` AS `title` 
			FROM `orders` 
			WHERE `orders_id` = ' . (int)$_GET['oID'];
		$query = xtc_db_query($sql);
		$moduleData = xtc_db_fetch_array($query);
		$moduleData['title'] = !empty($moduleData['title']) ? $moduleData['title'] : 'nopay';
        $moduleData['code'] = !empty($moduleData['code']) ? $moduleData['code'] : 'nopay';
		
		return $moduleData;
	}
	
	
	/**
	 * Loads the Gambio Hub order details JavaScript file.
	 *
	 * The script will adjust the order details page for Gambio Hub compatibility. Check the order_details.js for
	 * further information.
	 *
	 * @param array $module Contains the module "code" and "title" properties.
	 *
	 * @return GambioHubOrderExtender Returns same instance for chained method calls.
	 */
	protected function _addOrderDetailsScript(array $module)
	{
		$debug = file_exists(DIR_FS_CATALOG . '.dev-environment');
		
		$postfix = $debug ? '' : '.min';
		
		$url = DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl() . '/extenders/order_details' . $postfix
		       . '.js';
		
		$this->v_output_buffer['order_status'] = '<script src="' . $url . '" data-gambio-hub-payment-module="'
		                                         . htmlspecialchars_wrapper($module['code'])
		                                         . '" data-gambio-hub-payment-module-title="'
		                                         . htmlspecialchars_wrapper($module['title']) . '"></script>';
		
		return $this;
	}
	
	
	/**
	 * Add Gambio Hub order frontend iframe.
	 *
	 * Hub modules may display additional order information or offer extra options and actions to the user.
	 *
	 * @param array $module Contains the module "code" and "title" properties.
	 *
	 * @return GambioHubOrderExtender Returns same instance for chained method calls.
	 */
	protected function _addOrderFrontendIframe(array $module)
	{
		if(!array_key_exists('title', $module) || empty($module['title'])
		   || MODULE_PAYMENT_GAMBIO_HUB_STATUS === 'False')
		{
			return $this; // Do not add the iframe in non Hub modules or if Hub is not connected.
		}
		
		try
		{
			$clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
			$clientKey              = $clientKeyConfiguration->get(); // Will throw an exception if there's no Hub client key.
			$sessionsApiClient      = $this->_createSessionsApiClient();
			$authHash               = AuthHashCreator::create();
			$shopUrl                = HTTP_SERVER . DIR_WS_CATALOG;
			$languageCode           = new LanguageCode(new StringType(strtoupper(DEFAULT_LANGUAGE)));
			$sessionKey             = $sessionsApiClient->startSession($authHash, $shopUrl, $languageCode);
   
			/*
            if ((bool)gm_get_conf('MODULE_CENTER_CASHFLOWTECH_INSTALLED') === true) {
                $cftUrl = MODULE_PAYMENT_GAMBIO_HUB_URL . '/hub_client_keys/' . $clientKey . '/sessions/' . $sessionKey
                          . '/order_frontend/' . $_GET['oID'] . '?lang=' . $_SESSION['language_code']
                          . '&extramodule=CashFlowTechHub';
                
                $this->v_output_buffer['below_order_info'] .=
                    '<iframe id="hub-order-frontend-cft" src="' . $cftUrl . '" frameBorder="0" width="100%"></iframe>' .
		            '<script src="' . DIR_WS_CATALOG . 'GXModules/Gambio/Hub/Build/Admin/Javascript/vendor/iframe_resizer.js"></script>' .
		            '<script>iFrameResize({checkOrigin: false}, "#hub-order-frontend-cft")</script>';
            }
			*/
            
            if ($module['title'] !== 'nopay') {
                $url = MODULE_PAYMENT_GAMBIO_HUB_URL . '/hub_client_keys/' . $clientKey . '/sessions/' . $sessionKey
                       . '/order_frontend/' . $_GET['oID'] . '?lang=' . $_SESSION['language_code'];
                
                $this->v_output_buffer['below_order_info'] .=
                    '<iframe id="hub-order-frontend" src="' . $url . '" frameBorder="0" width="100%"></iframe>' .
			        '<script src="' . DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl() . '/vendor/iframe_resizer.js"></script>' .
                    '<script>iFrameResize({checkOrigin: false}, "#hub-order-frontend")</script>';
            }
		}
		catch(Exception $exception)
		{
			// Session creation has failed or there's no client key ... 
		}
		
		return $this;
	}
	
	
	/**
	 * Creates a HubSessionsApiClient instance.
	 *
	 * @return HubSessionsApiClient
	 */
	protected function _createSessionsApiClient()
	{
		$serviceFactory         = MainFactory::create('HubServiceFactory');
		$sessionKeyService      = $serviceFactory->createHubSessionKeyService();
		$clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
		$curlRequest            = new CurlRequest();
		$logControl             = LogControl::get_instance();
		$hubSettings            = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
		$sessionsApiClient      = MainFactory::create('HubSessionsApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
		                                              $sessionKeyService, $clientKeyConfiguration, $curlRequest,
		                                              $logControl, $hubSettings);
		
		return $sessionsApiClient;
	}
}
