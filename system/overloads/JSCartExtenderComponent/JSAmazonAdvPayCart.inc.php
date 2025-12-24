<?php
/* --------------------------------------------------------------
	JSAmazonAdvPayCart.inc.php 2021-07-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2021 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class JSAmazonAdvPayCart extends JSAmazonAdvPayCart_parent
{
	public function proceed()
	{
		parent::proceed();
		if($this->_amzadvpay_is_enabled())
		{
			$coo_aap = MainFactory::create_object('AmazonAdvancedPayment');
			//$t_widgets_url = $coo_aap->get_widgets_url();
			$t_seller_id = $coo_aap->seller_id;
			$t_hidden_button = $coo_aap->hidden_button == true ? 'true' : 'false';
			$cartContentType = $_SESSION['cart']->get_content_type();
			include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/AmazonAdvPayCart.js'));
		}
	}

	protected function _amzadvpay_is_enabled()
	{
        $t_is_enabled = defined('MODULE_PAYMENT_AMAZONADVPAY_STATUS') && filter_var(MODULE_PAYMENT_AMAZONADVPAY_STATUS, FILTER_VALIDATE_BOOLEAN);
        $t_is_enabled = $t_is_enabled && strpos(MODULE_PAYMENT_INSTALLED, 'amazonadvpay.php') !== false;
		return $t_is_enabled;
	}


}
