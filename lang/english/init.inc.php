<?php
/* --------------------------------------------------------------
   init.inc.php 2018-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.119 2003/05/19); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.25 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: english.php 1260 2005-09-29 17:48:04Z gwinger $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

@setlocale(LC_TIME, 'en_US.utf8', 'en_US.UTF-8', 'en_EN@euro', 'en_US', 'en-US', 'en', 'English');

$db               = StaticGXCoreLoader::getDatabaseQueryBuilder();
$languageSettings = $db->select()
                       ->from('languages')
                       ->where('languages_id', $_SESSION['languages_id'])
                       ->get()
                       ->row_array();
if($languageSettings !== null)
{
	define('DATE_FORMAT', $languageSettings['date_format']);
	define('DATE_FORMAT_LONG', $languageSettings['date_format_long']);
	define('DATE_FORMAT_SHORT', $languageSettings['date_format_short']);
	define('DATE_TIME_FORMAT', $languageSettings['date_time_format']);
	define('DOB_FORMAT_STRING', $languageSettings['dob_format_string']);
	define('HTML_PARAMS', $languageSettings['html_params']);
	define('LANGUAGE_CURRENCY', $languageSettings['language_currency']);
	define('PHP_DATE_TIME_FORMAT', $languageSettings['php_date_time_format']);
}

$coo_lang_file_master->init_from_lang_file('general');
$coo_lang_file_master->init_from_lang_file('gm_logger');
$coo_lang_file_master->init_from_lang_file('gm_shopping_cart');
$coo_lang_file_master->init_from_lang_file('gm_account_delete');
$coo_lang_file_master->init_from_lang_file('gm_price_offer');
$coo_lang_file_master->init_from_lang_file('gm_tell_a_friend');
$coo_lang_file_master->init_from_lang_file('gm_callback_service');
