<?php
/* --------------------------------------------------------------
   create_account.php 2023-02-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (install_step6.php,v 1.29 2003/08/20); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: install_step6.php 941 2005-05-11 19:49:53Z hhgag $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require_once(DIR_FS_INC . 'strlen_wrapper.inc.php');
require_once(DIR_FS_INC . 'strtoupper_wrapper.inc.php');
require_once(DIR_FS_INC . 'strtolower_wrapper.inc.php');
require_once(DIR_FS_INC . 'strpos_wrapper.inc.php');
require_once(DIR_FS_INC . 'substr_wrapper.inc.php');
require_once(DIR_FS_INC . 'xtc_rand.inc.php');
require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_env_info.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/logging/Debugger.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/caching/DataCache.inc.php');


// connect do database
xtc_db_connect() or die('Unable to connect to database server!');



// get configuration data
$configuration_query = xtc_db_query('select `key`, `value` from `gx_configurations` where `key` like "configuration/%";');
while ($configuration = xtc_db_fetch_array($configuration_query)) {
    $configurationKey = str_replace('configuration/', '', $configuration['key']);
    define($configurationKey, $configuration['value']);
}

$process = false;
if (isset($_POST['action']) && ($_POST['action'] == 'create_account')) {
	$process = true;

	$gender = xtc_db_prepare_input($_POST['GENDER'] ?? '');
	$firstname = xtc_db_prepare_input($_POST['FIRST_NAME'] ?? '');
	$lastname = xtc_db_prepare_input($_POST['LAST_NAME'] ?? '');
	$email_address = xtc_db_prepare_input($_POST['EMAIL_ADRESS'] ?? '');
	$street_address = xtc_db_prepare_input($_POST['STREET_ADRESS'] ?? '');
	$street_number = xtc_db_prepare_input(($_POST['STREET_NUMBER']) ?? '');
	$postcode = xtc_db_prepare_input($_POST['POST_CODE'] ?? '');
	$city = xtc_db_prepare_input($_POST['CITY'] ?? '');
	$zone_id = xtc_db_prepare_input($_POST['zone_id'] ?? '');
	$state = xtc_db_prepare_input($_POST['STATE'] ?? '');
	$country = xtc_db_prepare_input($_POST['COUNTRY'] ?? '');
	$telephone = xtc_db_prepare_input($_POST['TELEPHONE'] ?? '');
	$password = xtc_db_prepare_input($_POST['PASSWORD'] ?? '');
	$confirmation = xtc_db_prepare_input($_POST['PASSWORD_CONFIRMATION'] ?? '');
	$store_name = xtc_db_prepare_input($_POST['STORE_NAME'] ?? '');
	$email_from = xtc_db_prepare_input($_POST['EMAIL_ADRESS_FROM'] ?? '');
	$zone_setup = xtc_db_prepare_input($_POST['ZONE_SETUP'] ?? '');
	$company = xtc_db_prepare_input($_POST['COMPANY'] ?? '');
	$isErrorReportingAccepted = (xtc_db_prepare_input($_POST['CHECKBOX_ERROR_REPORTS']) === 'on');
	$acceptedShopInformationDataProcessing = xtc_db_prepare_input($_POST['ACCEPT_SHOP_INFORMATION_DATA_PROCESSING']) === 'true';

	$error = false;


	if (strlen_wrapper($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;
	}

	if (strlen_wrapper($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
		$error = true;
	}

	if (strlen_wrapper($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;
	} elseif (xtc_validate_email($email_address) == false) {
		$error = true;
	}



	if (strlen_wrapper($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
		$error = true;
	}
	
//	if(preg_match('/^\d+[a-zA-Z]{0,1}\z/', $street_number) != 1)
//	{
//		$error = true;
//	}

	if (strlen_wrapper($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
		$error = true;
	}

	if (strlen_wrapper($city) < ENTRY_CITY_MIN_LENGTH) {
		$error = true;
	}

	if (is_numeric($country) == false) {
		$error = true;
	}

	$zone_id = 0;
	$check_query = xtc_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int) $country . "'");
	$check = xtc_db_fetch_array($check_query);
	$entry_state_has_zones = ($check['total'] > 0);
	if ($entry_state_has_zones == true) {
		$zone_query = xtc_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int) $country . "' and (zone_name like '" . xtc_db_input($state) . "%' or zone_code like '%" . xtc_db_input($state) . "%')");
		if (xtc_db_num_rows($zone_query) > 0) {
			$zone = xtc_db_fetch_array($zone_query);
			$zone_id = $zone['zone_id'];
		} else {
			$error = true;
		}
	} else {
		if (strlen_wrapper($state) < ENTRY_STATE_MIN_LENGTH) {
			$error = true;
		}
	}

	if (strlen_wrapper($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
		$error = true;
	}


	if (strlen_wrapper($password) < ENTRY_PASSWORD_MIN_LENGTH) {
		$error = true;
	} elseif ($password != $confirmation) {
		$error = true;
	}

	if (strlen_wrapper($store_name) < '3') {
		$error = true;
	}
	if (strlen_wrapper($company) < '2') {
		$error = true;
	}

	if (strlen_wrapper($email_from) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;
	} elseif (xtc_validate_email($email_from) == false) {
		$error = true;
	}
	if (($zone_setup != 'yes') && ($zone_setup != 'no')) {
		$error = true;
	}


	if ($error == false) {

		xtc_db_query("insert into " . TABLE_CUSTOMERS . " (
										customers_id,
										customers_status,
										customers_firstname,
										customers_lastname,
										customers_gender,
										customers_email_address,
										customers_default_address_id,
										customers_telephone,
										customers_password,
										customers_company,
										delete_user,
										customers_date_added) VALUES
										('1',
										'0',
										'" . xtc_db_input($firstname) . "',
										'" . xtc_db_input($lastname) . "',
										'" . xtc_db_input($gender) . "',
										'" . xtc_db_input($email_address) . "',
										'1',
										'" . xtc_db_input($telephone) . "',
										'" . xtc_encrypt_password($password) . "',
										'" . xtc_db_input($company) . "',
										'0',
										NOW())");

		xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (
										customers_info_id,
										customers_info_date_of_last_logon,
										customers_info_number_of_logons,
										customers_info_date_account_created,
										customers_info_date_account_last_modified) VALUES
									    ('1',now(),'0',now(),now())");
		xtc_db_query("insert into " . TABLE_ADDRESS_BOOK . " (
										customers_id,
										entry_company,
										entry_gender,
   										entry_firstname,
   										entry_lastname,
   										entry_street_address,
   										entry_postcode,
   										entry_city,
   										entry_state,
   										entry_country_id,
   										entry_zone_id) VALUES
										('1',
										'" . xtc_db_input($company) . "',
										'" . xtc_db_input($gender) . "',
										'" . xtc_db_input($firstname) . "',
										'" . xtc_db_input($lastname) . "',
										'" . xtc_db_input(trim($street_address . ' ' . $street_number)) . "',
										'" . xtc_db_input($postcode) . "',
										'" . xtc_db_input($city) . "',
										'" . xtc_db_input($state) . "',
										'" . xtc_db_input($country) . "',
										'" . xtc_db_input($zone_id) . "'
										)");
        xtc_db_query("INSERT INTO admin_access_users (
										customer_id,
										admin_access_role_id) VALUES
									    (1, 1)");

		xtc_db_query("UPDATE countries SET status = 0");
		xtc_db_query("UPDATE countries SET status = 1 WHERE countries_id = '" . (int)$country . "'");

		// Todo: Fix it all here!
		
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_address) . "' WHERE `key` = 'configuration/CONTACT_US_REPLY_ADDRESS'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_address) . "' WHERE `key` = 'configuration/EMAIL_SUPPORT_REPLY_ADDRESS'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_address) . "' WHERE `key` = 'configuration/EMAIL_BILLING_REPLY_ADDRESS'");

		xtc_db_query("UPDATE gx_configurations SET `value` = '" . gm_local_install_path() . "admin/backups/page_parse_time.log' WHERE `key` = 'configuration/STORE_PAGE_PARSE_TIME_LOG'");

		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/CONTACT_US_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/CONTACT_US_REPLY_ADDRESS_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/EMAIL_SUPPORT_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/EMAIL_SUPPORT_REPLY_ADDRESS_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/EMAIL_BILLING_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/EMAIL_BILLING_REPLY_ADDRESS_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/STORE_OWNER'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($company) . "' WHERE `key` = 'configuration/COMPANY_NAME'");
		
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($firstname) . "' WHERE `key` = 'configuration/TRADER_FIRSTNAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($lastname) . "' WHERE `key` = 'configuration/TRADER_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($street_address) . "' WHERE `key` = 'configuration/TRADER_STREET'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($street_number) . "' WHERE `key` = 'configuration/TRADER_STREET_NUMBER'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($postcode) . "' WHERE `key` = 'configuration/TRADER_ZIPCODE'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($city) . "' WHERE `key` = 'configuration/TRADER_LOCATION'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($telephone) . "' WHERE `key` = 'configuration/TRADER_TEL'");

		xtc_db_query("UPDATE gx_configurations SET `value`='" . (int) $country . "' WHERE `key` = 'configuration/STORE_COUNTRY'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . (int) $zone_id . "' WHERE `key` = 'configuration/STORE_ZONE'");

		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_address) . "' WHERE `key` = 'configuration/STORE_OWNER_EMAIL_ADDRESS'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($store_name) . "' WHERE `key` = 'configuration/STORE_NAME'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_from) . "' WHERE `key` = 'configuration/EMAIL_FROM'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($country) . "' WHERE `key` = 'configuration/SHIPPING_ORIGIN_COUNTRY'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($postcode) . "' WHERE `key` = 'configuration/SHIPPING_ORIGIN_ZIP'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_from) . "' WHERE `key` = 'configuration/EMAIL_BILLING_ADDRESS'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_from) . "' WHERE `key` = 'configuration/CONTACT_US_EMAIL_ADDRESS'");
		xtc_db_query("UPDATE gx_configurations SET `value`='" . xtc_db_input($email_from) . "' WHERE `key` = 'configuration/EMAIL_SUPPORT_ADDRESS'");


		$configPath = DIR_FS_CATALOG . 'GXModules/Gambio/ErrorReporting/configuration.json';
		
		if(is_writable(dirname($configPath)))
		{
			if(file_exists($configPath))
			{
				unlink($configPath);
			}
			
			$config = [
				'active' => $isErrorReportingAccepted,
				'dsn'    => 'https://22a09c39ec104b93a0624e18f61055fe@telemetry.gambio-server.net/6'
			];
			
			file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		}
		
		xtc_db_query("
				INSERT INTO
					`gx_configurations` (`key`,`value`)
				VALUES
					('gm_configuration/MODULE_CENTER_ERRORREPORTING_INSTALLED', '" . ($isErrorReportingAccepted ? '1' : '0') . "');
			");
		
		xtc_db_query("UPDATE `gx_configurations`
                        SET `value` = '" . ($acceptedShopInformationDataProcessing ? "true" : "false") . "'
                        WHERE `key` = 'gm_configuration/ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING';");
	}
}