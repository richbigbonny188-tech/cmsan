<?php
/* --------------------------------------------------------------
   KlarnaHubOrdersOverviewController.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaHubOrdersOverviewController extends KlarnaHubOrdersOverviewController_parent
{
	protected function _getAssetsArray()
	{
		$assetsArray = parent::_getAssetsArray();
		
		$installedVersion = gm_get_conf('INSTALLED_VERSION');
		$hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);
		
		$debug = file_exists(DIR_FS_CATALOG . '.dev-environment');
		
		$postfix = $debug ? '' : '.min';
		
		$queryParams = [
			'appUrl'              => DIR_WS_CATALOG,
			'assetsCompatibility' => '.js' // This parameter has to be at the end for Asset class compatibility.
		];
		
		$baseUrl = $hubAssetHelper->getScriptsBaseUrl(); 
		
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/vendor/fetch'
		                                              . $postfix . '.js');
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/extenders/klarna_hub/klarna_hub'
		                                              . $postfix . '.js?' . http_build_query($queryParams, '', '&'));
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/extenders/klarna_hub/klarna_hub_api'
		                                              . $postfix . '.js');
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/extenders/klarna_hub/klarna_hub_lib'
		                                              . $postfix . '.js');
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/extenders/klarna_hub/order_overview/extend_cancel_order_action'
		                                              . $postfix . '.js');
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/extenders/klarna_hub/order_overview/extend_delete_order_action'
		                                              . $postfix . '.js');
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/extenders/klarna_hub/order_overview/extend_order_status_modal'
		                                              . $postfix . '.js');
		$assetsArray[] = MainFactory::create('Asset', DIR_WS_CATALOG
		                                              . $baseUrl . '/extenders/klarna_hub/order_overview/extend_tracking_codes_modal'
		                                              . $postfix . '.js');
		
		return $assetsArray;
	}
}