<?php
/* --------------------------------------------------------------
   GambioHubInvoicesOverviewAjaxController.inc.php 2017-04-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubInvoicesOverviewAjaxController
 *
 * Updates the data representation in the invoices main page.
 */
class GambioHubInvoicesOverviewAjaxController extends GambioHubInvoicesOverviewAjaxController_parent
{
	/**
	 * Adds support for Gambio Hub payment module titles and aliases.
	 *
	 * @return mixed
	 */
	protected function _getTableData()
	{
		$tableData = parent::_getTableData();
		
		// Fetch the available hub modules from orders table. 
		$queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		$orderIds = [];
		foreach($tableData as &$row)
		{
			$orderIds[] = $row['orderId'];
		}
		
		if(empty($orderIds))
		{
			return $tableData;
		}
		
		$modules = $queryBuilder->select('orders_id, gambio_hub_module, gambio_hub_module_title')
		                        ->from('orders')
		                        ->where('orders_id IN (' . implode(',', $orderIds)
		                                . ') AND gambio_hub_module_title != ""')
		                        ->get()
		                        ->result_array();
		
		foreach($tableData as &$row)
		{
			foreach($modules as $module)
			{
				if((int)$module['orders_id'] === $row['orderId'])
				{
					$aliasKey = 'MODULE_PAYMENT_GAMBIO_HUB_' . strtoupper($module['gambio_hub_module']) . '_ALIAS';
					
					$row['paymentMethod'] = gm_get_conf($aliasKey) ? : $module['gambio_hub_module_title'];
					$row['DT_RowData']['paymentMethod'] = $module['gambio_hub_module_title'];
					
					break;
				}
			}
		}
		
		return $tableData;
	}
}