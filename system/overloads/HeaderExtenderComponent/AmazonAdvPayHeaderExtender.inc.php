<?php
/* --------------------------------------------------------------
	AmazonAdvPayHeaderExtender.inc.php 2023-04-18
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2023 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonAdvPayHeaderExtender extends AmazonAdvPayHeaderExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		$isCheckoutPage = strpos($_SERVER['SCRIPT_NAME'], 'shopping_cart.php') !== false;
		$isCheckoutPage = $isCheckoutPage || strpos($_SERVER['SCRIPT_NAME'], 'checkout_') !== false;
		$loginAndPayMode = !empty($_COOKIE['amazon_Login_accessToken']) && ($_SESSION['payment'] ?? null) === 'amazonadvpay';
		if($this->_amzadvpay_is_enabled() === true && $isCheckoutPage && $loginAndPayMode === false)
		{
			$coo_aap = MainFactory::create_object('AmazonAdvancedPayment');
			$t_widgets_url = $coo_aap->get_widgets_url();
			$this->v_output_buffer['amzwidgets'] = '<script src="'.$t_widgets_url.'"></script>'.PHP_EOL;
		}
	}

	protected function _amzadvpay_is_enabled()
	{
	    $t_is_enabled = defined('MODULE_PAYMENT_AMAZONADVPAY_STATUS') && filter_var(MODULE_PAYMENT_AMAZONADVPAY_STATUS, FILTER_VALIDATE_BOOLEAN);
		$t_is_enabled = $t_is_enabled && defined('MODULE_PAYMENT_INSTALLED') && strpos(MODULE_PAYMENT_INSTALLED, 'amazonadvpay.php') !== false;
		return $t_is_enabled;
	}

}
