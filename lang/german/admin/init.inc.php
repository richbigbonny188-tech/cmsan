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
   (c) 2002-2003 osCommerce(german.php,v 1.99 2003/05/28); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.24 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: german.php 905 2005-04-29 13:02:06Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

@setlocale(LC_TIME, 'de_DE.utf8', 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de-DE', 'de', 'ge', 'German');

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

$coo_lang_file_master->init_from_lang_file('admin_general');
$coo_lang_file_master->init_from_lang_file('gm_general');
