<?php
/* --------------------------------------------------------------
   GambioHubInvoiceListGenerator.inc.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubInvoiceListGenerator
 *
 * Enables invoice filtering with Gambio Hub modules.
 */
class GambioHubInvoiceListGenerator extends GambioHubInvoiceListGenerator_parent
{
	/**
	 * Filter the invoice records.
	 *
	 * This method contains the filtering logic. It can be overloaded in order to provide a custom filtering logic.
	 *
	 * @param array           $filterParameters Contains the column slug-names and their values.
	 * @param IntType|null    $startIndex       The start index of the wanted array to be returned (default = null).
	 * @param IntType|null    $maxCount         Maximum amount of items which should be returned (default = null).
	 * @param StringType|null $orderBy          A string which defines how the items should be ordered (default = null).
	 *
	 * @return CI_DB_result
	 *
	 * @throws BadMethodCallException
	 */
	protected function _filter(array $filterParameters,
	                           IntType $startIndex = null,
	                           IntType $maxCount = null,
	                           StringType $orderBy = null)
	{
        if(!isset($filterParameters['paymentMethod']) || !is_array($filterParameters['paymentMethod']))
		{
			return parent::_filter($filterParameters, $startIndex, $maxCount, $orderBy); // No payment method filtering
		}
		
		// Extract Gambio Hub options from filtering (will be handled by this method). 
		$hubPaymentMethods = [];
        
        // None Gambio Hub payment methods.
        $paymentMethods = [];
		
		foreach($filterParameters['paymentMethod'] as $index => $paymentMethod)
		{
			if(strpos($paymentMethod, 'Hub') !== false)
			{
				$hubPaymentMethods[] = $paymentMethod;
			}
            else
            {
                $paymentMethods[] = $paymentMethod;
            }
            unset($filterParameters['paymentMethod'][$index]);
		}
		
		if (empty($filterParameters['paymentMethod']))
		{
			// Disable payment method filtering as there aren't any options left.
			unset($filterParameters['paymentMethod']);
		}
		
		$this->db->group_start()->where('orders.gambio_hub_module', array_shift($hubPaymentMethods));
		foreach($hubPaymentMethods as $hubPaymentMethod)
		{
			$this->db->or_where('orders.gambio_hub_module', $hubPaymentMethod);
		}
        foreach($paymentMethods as $paymentMethod)
        {
            $this->db->or_where('orders.payment_method', $paymentMethod);
        }
		$this->db->group_end();
		
		return parent::_filter($filterParameters, $startIndex, $maxCount, $orderBy);
	}
}
