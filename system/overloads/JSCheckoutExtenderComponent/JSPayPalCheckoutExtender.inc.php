<?php
/* --------------------------------------------------------------
	JSPayPalCheckoutExtender.inc.php 2021-07-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2021 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This class causes additional Javascript to be included for PayPal Plus.
 */
class JSPayPalCheckoutExtender extends JSPayPalCheckoutExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		if($this->_ppplus_is_enabled())
		{
			include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/PayPalCheckout.js'));
		}
	}

	protected function _ppplus_is_enabled()
	{
	    $t_is_enabled = defined('MODULE_PAYMENT_PAYPAL3_STATUS') && filter_var(MODULE_PAYMENT_PAYPAL3_STATUS, FILTER_VALIDATE_BOOLEAN);
		$t_is_enabled = $t_is_enabled && strpos(MODULE_PAYMENT_INSTALLED, 'paypal3.php') !== false;
		return $t_is_enabled;
	}

}
