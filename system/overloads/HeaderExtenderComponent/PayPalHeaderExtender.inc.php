<?php
/* --------------------------------------------------------------
	PayPalHeaderExtender.inc.php 2021-07-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2021 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This HeaderExtender inserts required Javascript and styles for PayPal Plus.
 */
class PayPalHeaderExtender extends PayPalHeaderExtender_parent
{
    /**
     * @var string
     */
    protected $payPalModuleCode = 'PayPalHub';


    public function proceed()
    {
        parent::proceed();
        $isCheckoutPage = strpos($_SERVER['SCRIPT_NAME'], 'shopping_cart.php') !== false;
        $isCheckoutPage = $isCheckoutPage || strpos($_SERVER['SCRIPT_NAME'], 'checkout_') !== false;
        if ($isCheckoutPage && $this->_ppplus_is_enabled()) {
            $output_array = array();
            $output_array['ppplusscript'] = '<script src="https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js" type="text/javascript"></script>' . PHP_EOL;

            $href = xtc_href_link(
                StaticGXCoreLoader::getThemeControl()->getThemeCssPath() . 'paypalplus.css',
                '',
                'SSL',
                true,
                true,
                false,
                true,
                true
            );
            $output_array['ppplusstyles'] = '<link href="' . $href . '" rel="stylesheet" type="text/css">';

            if (!is_array($this->v_output_buffer)) {
                $this->v_output_buffer = array();
            }
            $this->v_output_buffer = array_merge($this->v_output_buffer, $output_array);

            include DIR_FS_CATALOG . 'release_info.php';
            if (version_compare($gx_version, 'v2.1') <= 0) {
                foreach ($output_array as $output) {
                    echo $output;
                }
            }
        }
    }

    protected function _ppplus_is_enabled()
    {
        $paypalConfiguration = MainFactory::create('PayPalConfigurationStorage');

        $ppplusIsEnabled = false;
        $paymentModuleIsEnabled = defined('MODULE_PAYMENT_PAYPAL3_STATUS')
                                  && filter_var(MODULE_PAYMENT_PAYPAL3_STATUS,
                                                FILTER_VALIDATE_BOOLEAN);
        $paymentModuleIsEnabled = $paymentModuleIsEnabled && strpos(MODULE_PAYMENT_INSTALLED, 'paypal3.php') !== false;
        $isShortcutPayment = isset($_SESSION['paypal_payment']) && $_SESSION['paypal_payment']['type'] == 'ecs';
        $isSelfPickup = isset($_SESSION['shipping']) && is_array($_SESSION['shipping']) && $_SESSION['shipping']['id'] == 'selfpickup_selfpickup';
        $selfPickupAllowed = $paypalConfiguration->get('allow_selfpickup') == true;
        $stateRequiredCountries = explode(',', 'AR,BR,CA,CN,ID,IN,JP,MX,TH,US');
        $stateRequired = isset($GLOBALS['order']) 
            && is_array($GLOBALS['order']->delivery['country'])
            && in_array($GLOBALS['order']->delivery['country']['iso_code_2'], $stateRequiredCountries)
            && empty($GLOBALS['order']->delivery['zone_id']);

        if ($paymentModuleIsEnabled === true && $isShortcutPayment === false) {
            $usePayPalPlus = $paypalConfiguration->get('use_paypal_plus') == true;
        } else {
            $usePayPalPlus = false;
        }

        $useHubPayPalPlus = false;

        if (array_key_exists('gambio_hub_payments', $_SESSION)
            && array_key_exists($this->payPalModuleCode, $_SESSION['gambio_hub_payments'])
        ) {
            $useHubPayPalPlus = $_SESSION['gambio_hub_payments'][$this->payPalModuleCode]['configuration']['use_plus'];
        }

        $ppplusIsEnabled = $useHubPayPalPlus || ((isset($_SESSION['ppplus_disabled']) === false) && $paymentModuleIsEnabled && $usePayPalPlus && (!$isSelfPickup || $selfPickupAllowed) && !$stateRequired);
        return $ppplusIsEnabled;
    }

}
