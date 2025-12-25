<?php
/* --------------------------------------------------------------
   init.inc.php 2022-07-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.119 2003/05/19); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.25 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: german.php 1308 2005-10-15 14:22:18Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

@setlocale(LC_TIME, 'de_DE.utf8', 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de-DE', 'de', 'ge', 'German');

$db               = StaticGXCoreLoader::getDatabaseQueryBuilder();
$languageSettings = $db->select()
                       ->from('languages')
                       ->where('languages_id', $_SESSION['languages_id'])
                       ->get()
                       ->row_array();
if($languageSettings !== null)
{
	
    defined('DATE_FORMAT') ?: define('DATE_FORMAT', $languageSettings['date_format']);
	defined('DATE_FORMAT_LONG') ?: define('DATE_FORMAT_LONG', $languageSettings['date_format_long']);
	defined('DATE_FORMAT_SHORT') ?: define('DATE_FORMAT_SHORT', $languageSettings['date_format_short']);
	defined('DATE_TIME_FORMAT') ?: define('DATE_TIME_FORMAT', $languageSettings['date_time_format']);
	defined('DOB_FORMAT_STRING') ?: define('DOB_FORMAT_STRING', $languageSettings['dob_format_string']);
	defined('HTML_PARAMS') ?: define('HTML_PARAMS', $languageSettings['html_params']);
	defined('LANGUAGE_CURRENCY') ?: define('LANGUAGE_CURRENCY', $languageSettings['language_currency']);
    defined('PHP_DATE_TIME_FORMAT') ?: define('PHP_DATE_TIME_FORMAT', $languageSettings['php_date_time_format']);
}

$coo_lang_file_master->init_from_lang_file('general');
$coo_lang_file_master->init_from_lang_file('gm_logger');
$coo_lang_file_master->init_from_lang_file('gm_shopping_cart');
$coo_lang_file_master->init_from_lang_file('gm_account_delete');
$coo_lang_file_master->init_from_lang_file('gm_price_offer');
$coo_lang_file_master->init_from_lang_file('gm_tell_a_friend');
$coo_lang_file_master->init_from_lang_file('gm_callback_service');
