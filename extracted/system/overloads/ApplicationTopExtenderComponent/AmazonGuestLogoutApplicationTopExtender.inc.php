<?php
/* --------------------------------------------------------------
	AmazonGuestLogoutApplicationTopExtender.inc.php 2019-10-09
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonGuestLogoutApplicationTopExtender extends AmazonGuestLogoutApplicationTopExtender_parent
{
	protected $amazonAdvancedPayment;

	public function proceed()
	{
		parent::proceed();

		$basename_self = basename($GLOBALS['PHP_SELF']);

		if($basename_self === 'shop.php')
		{
			$allowedDos = ['CartDropdown', 'Cart', 'CookieBar', 'GoogleAnalyticsProxy'];
			if(in_array($_GET['do'], $allowedDos, true))
			{
				return;
			}
		}

		$isCheckoutPage =
			strpos($basename_self, 'checkout') !== false ||
			strpos($basename_self, 'request_port') !== false ||
			strpos($basename_self, 'mailhive') !== false ||
			strpos($basename_self, 'itrk') !== false ||
			strpos($basename_self, 'janolaw') !== false ||
			strpos($basename_self, 'gm_javascript') !== false;
		$isAmazonGuest = isset($_SESSION['amazonadvpay_guest']) && (bool)$_SESSION['amazonadvpay_guest'] === true;

		if($isAmazonGuest && !$isCheckoutPage)
		{
			$this->amazonAdvancedPayment = MainFactory::create_object('AmazonAdvancedPayment');
			$this->amazonAdvancedPayment->log('Amazon guest left checkout, destroying guest account, PHP_SELF = '.$GLOBALS['PHP_SELF'].' REQUEST_URI: '.$_SERVER['REQUEST_URI']);
			$this->deleteAmazonGuestAccount($_SESSION['customer_id']);
			
			unset(
				$_SESSION['amazonadvpay_order_ref_id'],
				$_SESSION['sendto'],
				$_SESSION['billto'],
				$_SESSION['payment']
			);
			
			if(isset($_SESSION['amazonadvpay_guest']))
			{
				unset(
					$_SESSION['account_type'],
					$_SESSION['customer_id'],
					$_SESSION['customer_first_name'],
					$_SESSION['customer_last_name'],
					$_SESSION['customer_default_address_id'],
					$_SESSION['customer_country_id'],
					$_SESSION['customer_zone_id'],
					$_SESSION['customer_vat_id'],
					$_SESSION['amazonadvpay_guest'],
					$_SESSION['amazonadvpay_logout_guest']
				);
			}
		}
	}

	protected function deleteAmazonGuestAccount($customers_id)
	{
		$this->amazonAdvancedPayment->delete_amazon_address_book_entries($customers_id);
		xtc_db_query('DELETE FROM customers WHERE customers_id = \''.(int)$customers_id.'\'');
		xtc_db_query('DELETE FROM customers_info WHERE customers_info_id = \''.(int)$customers_id.'\'');
		xtc_db_query('DELETE FROM address_book WHERE customers_id = \''.(int)$customers_id.'\'');
	}
}
