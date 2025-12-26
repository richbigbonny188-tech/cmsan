<?php
/* --------------------------------------------------------------
   GambioHubCheckoutConfirmationThemeContentView.inc.php 2023-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'inc/get_transfer_charge_text.inc.php'; // Required in older shop versions.

/**
 * Class GambioHubCheckoutConfirmationThemeContentView
 */
class GambioHubCheckoutConfirmationThemeContentView extends GambioHubCheckoutConfirmationThemeContentView_parent
{
	/**
	 * @var string
	 */
	protected $cashOnDeliveryModuleCode = 'CashOnDeliveryHub';
	
	
	public function prepare_data()
	{
		parent::prepare_data();
		
		if(($_SESSION['payment'] ?? null) === 'gambio_hub' && isset($_SESSION['gambio_hub_selection']))
		{
			$this->content_array['PAYMENT_METHOD'] = $_SESSION['gambio_hub_payments'][$_SESSION['gambio_hub_selection']]['title'];
			if (isset($_SESSION['gambio_hub_subselection'])) {
			    if (isset($_SESSION['gambio_hub_payments_submodules'][$_SESSION['gambio_hub_selection']][$_SESSION['gambio_hub_subselection']])) {
			        $submoduleInfo = $_SESSION['gambio_hub_payments_submodules'][$_SESSION['gambio_hub_selection']][$_SESSION['gambio_hub_subselection']];
                    $this->content_array['PAYMENT_METHOD'] = $submoduleInfo['title'];
                }
            }
			
			if($_SESSION['gambio_hub_selection'] === $this->cashOnDeliveryModuleCode)
			{
				$this->content_array['COD_INFO'] = get_transfer_charge_text($this->coo_order->info['shipping_class'],
				                                                            $this->coo_order->delivery['country']['iso_code_2'],
				                                                            $this->coo_xtc_price->cStatus['customers_status_id'],
				                                                            $this->coo_xtc_price->actualCurr);
			}
		}
	}
}
