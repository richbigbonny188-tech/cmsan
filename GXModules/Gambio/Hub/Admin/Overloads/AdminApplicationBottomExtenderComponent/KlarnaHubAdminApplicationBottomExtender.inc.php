<?php
/* --------------------------------------------------------------
   KlarnaHubAdminApplicationBottomExtender.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class KlarnaHubAdminApplicationBottomExtender
 */
class KlarnaHubAdminApplicationBottomExtender extends KlarnaHubAdminApplicationBottomExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		
		$installedVersion = gm_get_conf('INSTALLED_VERSION');
		$hubAssetHelper = MainFactory::create('HubAssetHelper', $installedVersion);
		
		$requestUri = basename($_SERVER['REQUEST_URI']);
		$scriptName = substr($requestUri, 0,
			(strpos($requestUri, '?') !== false ? strpos($requestUri, '?') : strlen($requestUri)));
		
		if($scriptName === 'orders_edit.php' && array_key_exists('oID', $_GET))
		{
			// If this is a KlarnaHub order load the klarna_hub_orders_edit.js extender script.
			$order = StaticGXCoreLoader::getDatabaseQueryBuilder()
			                           ->get_where('orders', ['orders_id' => $_GET['oID']])
			                           ->row_array();
			
			$klarnaHubModules = [
				'KlarnaHub',
				'KlarnaSliceitHub',
				'KlarnaPaynowHub',
				'KlarnaPaylaterHub',
				'KlarnaBanktransferHub'
			];
			
			if($order && array_key_exists('gambio_hub_module', $order)
			   && in_array($order['gambio_hub_module'], $klarnaHubModules))
			{
				$baseUrl = HTTP_SERVER . DIR_WS_CATALOG
				           . $hubAssetHelper->getScriptsBaseUrl();
				
				$queryParams = [
					'appUrl'      => DIR_WS_CATALOG,
					'moduleCode'  => $order['gambio_hub_module'],
					'orderNumber' => $_GET['oID']
				];
				
				$postfix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
				
				$script = '
					<script src="' . $baseUrl . '/vendor/fetch' . $postfix . '.js"></script>
					<script src="' . $baseUrl . '/extenders/klarna_hub/klarna_hub' . $postfix . '.js?' . http_build_query($queryParams, '', '&') . '"></script>
					<script src="' . $baseUrl . '/extenders/klarna_hub/klarna_hub_api' . $postfix . '.js"></script>
					<script src="' . $baseUrl . '/extenders/klarna_hub/klarna_hub_lib' . $postfix . '.js"></script>
                    <script src="' . $baseUrl . '/extenders/klarna_hub/order_edit/disable_other_order_edit_buttons' . $postfix . '.js"></script>
                    <script src="' . $baseUrl . '/extenders/klarna_hub/order_edit/extend_order_edit_form' . $postfix . '.js"></script>
                    <script src="' . $baseUrl . '/extenders/klarna_hub/order_edit/hide_new_product_table' . $postfix . '.js"></script>
                    <script src="' . $baseUrl . '/extenders/klarna_hub/order_edit/disable_higher_order_amounts' . $postfix . '.js"></script>
				';
				
				echo $script;
			}
		}
	}
}
