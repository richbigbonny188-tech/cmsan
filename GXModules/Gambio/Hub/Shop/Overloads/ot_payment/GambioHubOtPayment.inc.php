<?php
/* --------------------------------------------------------------
   GambioHubOtPayment.inc.php 2023-05-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubOtPayment
 */
class GambioHubOtPayment extends GambioHubOtPayment_parent
{
	/**
	 * Set the $_SESSION['payment'] value temporarily to hub payment module code to extend ot_payment functionality for
	 * hub payments
	 * 
	 * @param $amount
	 *
	 * @return array
	 */
	public function calculate_credit($amount)
	{
        if (!isset($_SESSION['payment'])) {
            return ['sum' => 0];
        }
        
	    $incompatibleHubModules = ['KlarnaPaylaterHub', 'KlarnaSliceitHub', 'KlarnaPaynowHub', 'KlarnaBanktransferHub'];
	    
		$sessionPaymentBackup = $_SESSION['payment'];
        
        if ($_SESSION['payment'] === 'gambio_hub' && isset($_SESSION['gambio_hub_selection'])
            && !in_array($_SESSION['gambio_hub_selection'], $incompatibleHubModules, true)) {
            $_SESSION['payment'] = $_SESSION['gambio_hub_selection'];
        }
		
		$discount = parent::calculate_credit($amount);
		
		$_SESSION['payment'] = $sessionPaymentBackup;
		
		return $discount;
	}
}
