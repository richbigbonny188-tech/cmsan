<?php
/* --------------------------------------------------------------
   GambioHubInvoicesOverviewColumns.inc.php 2017-04-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubInvoicesOverviewColumns
 *
 * Enables correct GambioHub filtering in the invoices overview page.
 *
 * Notice: This controller will only be used from shops starting from v3.1.1.0.
 */
class GambioHubInvoicesOverviewColumns extends GambioHubInvoicesOverviewColumns_parent
{
	/**
	 * Adds Gambio Hub module options for filtering.
	 *
	 * @return array
	 */
	public function _getPaymentOptions()
	{
		$paymentOptions = parent::_getPaymentOptions();
		
		// Remove generic "Gambio Hub" option.
		foreach($paymentOptions as $index => $paymentOption)
		{
			if($paymentOption['value'] === 'gambio_hub')
			{
				unset($paymentOptions[$index]);
			}
		}
		
		// Add Hub module options.  
		$queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		$hubModules = $queryBuilder->distinct()
		                           ->select('gambio_hub_module, gambio_hub_module_title')
		                           ->where('payment_class', 'gambio_hub')
		                           ->get('orders')
		                           ->result_array();
		
		foreach($hubModules as $hubModule)
		{
			$aliasKey = 'MODULE_PAYMENT_GAMBIO_HUB_' . strtoupper($hubModule['gambio_hub_module']) . '_ALIAS';
			
			$paymentOptions[] = [
				'value' => $hubModule['gambio_hub_module'],
				'text'  => gm_get_conf($aliasKey) ? gm_get_conf($aliasKey) . ': ' . $hubModule['gambio_hub_module_title'] : $hubModule['gambio_hub_module_title']
			];
		}
		
		return $paymentOptions;
	}
}