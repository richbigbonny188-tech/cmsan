<?php
/* --------------------------------------------------------------
   KlarnaHubAdminApplicationTopPrimalExtenderComponent.inc.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaHubAdminApplicationTopPrimalExtenderComponent
	extends KlarnaHubAdminApplicationTopPrimalExtenderComponent_parent
{
	public function proceed()
	{
		parent::proceed();
		
		// Disable order edit page for KlarnaHub orders.
		$isOrderEdit       = array_key_exists('SCRIPT_FILENAME', $_SERVER)
		                     && basename($_SERVER['SCRIPT_FILENAME']) === 'orders_edit.php';
		$isUnallowedAction = array_key_exists('edit_action', $_GET)
		                     && ($_GET['edit_action'] === 'address'
		                         || $_GET['edit_action'] === 'other');
		
		if($isOrderEdit && $isUnallowedAction)
		{
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
				header('Location: ' . DIR_WS_ADMIN . 'orders.php?oID=' . $order['orders_id'] . '&action=edit');
			}
		}
	}
}
