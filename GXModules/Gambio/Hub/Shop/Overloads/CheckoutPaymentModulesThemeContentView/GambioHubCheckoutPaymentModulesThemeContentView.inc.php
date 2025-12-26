<?php
/* --------------------------------------------------------------
   GambioHubCheckoutPaymentModulesThemeContentView.inc.php 2022-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubCheckoutPaymentModulesThemeContentView
 */
class GambioHubCheckoutPaymentModulesThemeContentView extends GambioHubCheckoutPaymentModulesThemeContentView_parent
{
	/**
	 * @var string
	 */
	protected $payPalModuleCode = 'PayPal2Hub';


	/**
	 * Adds gambio hub module code to selected payment method name to make preselection of radio input possible.
	 *
	 * @param $paymentMethod
	 */
	public function set_selected_payment_method($paymentMethod)
	{
		if($paymentMethod === 'gambio_hub' && array_key_exists('gambio_hub_selection', $_SESSION))
		{
			$paymentMethod .= '-' . $_SESSION['gambio_hub_selection'];
		}

		parent::set_selected_payment_method($paymentMethod);
	}


	/**
	 * Adds new variable for Smarty, which defines if Paypal Plus is currently in use or not.
	 */
	public function prepare_data()
	{
		parent::prepare_data();

		if(array_key_exists('gambio_hub_payments', $_SESSION)
		   && array_key_exists($this->payPalModuleCode, $_SESSION['gambio_hub_payments'])
		)
		{
		    $plusEnabledInConfiguration =
                ($_SESSION['gambio_hub_payments'][$this->payPalModuleCode]['configuration']['use_plus'] ?? null) === 'true'
                || ($_SESSION['gambio_hub_payments'][$this->payPalModuleCode]['configuration']['usePlus'] ?? null) === 'true';
		    $paymentApproved = isset($_SESSION['PayPal2Hub']['paymentID'], $_SESSION['PayPal2Hub']['payerID']);
            $orderApproved = isset($_SESSION['PayPal2Hub']['orderID'], $_SESSION['PayPal2Hub']['payerID']);
		    $usePayPalPlus = $plusEnabledInConfiguration && !$orderApproved && !$paymentApproved;
			$this->set_content_data('use_paypal_plus', var_export($usePayPalPlus, true));
		}
	}
	
	
    protected function getGambioHubJavascriptPathName($fileBaseName)
    {
        if (file_exists(DIR_FS_CATALOG . '.dev-environment')) {
            $path = DIR_FS_CATALOG . 'GXModules/Gambio/Hub/Shop/Javascript/' . $fileBaseName . '.js';
        } else {
            $path = DIR_FS_CATALOG . 'GXModules/Gambio/Hub/Build/Shop/Javascript/'. $fileBaseName . '.min.js';
        }
        return $path;
    }
}
