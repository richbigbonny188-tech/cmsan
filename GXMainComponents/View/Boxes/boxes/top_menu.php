<?php
/* --------------------------------------------------------------
  top_menu.php 2020-05-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

$coo_login_dropdown = MainFactory::create_object('LoginBoxThemeContentView');
$coo_login_dropdown->set_login_dropdown_template();
$coo_login_dropdown->set_return_url($_GET['return_url'] ?? '');
$t_login_dropdown_html = $coo_login_dropdown->get_html();
$this->set_content_data('LOGIN_DROPDOWN', $t_login_dropdown_html);

$t_currencies_dropdown_html = '';

if (gm_get_conf('SHOW_TOP_CURRENCY_SELECTION') == 'true') {
    $coo_currencies_dropdown = MainFactory::create_object('CurrenciesBoxThemeContentView');
    $coo_currencies_dropdown->setXtcPrice($this->coo_xtc_price);
    $coo_currencies_dropdown->setRequestType($this->request_type);
    if (isset($_GET)) {
        $coo_currencies_dropdown->setGetArray($_GET);
    } else {
        $coo_currencies_dropdown->setGetArray([]);
    }
    $coo_currencies_dropdown->set_currency_dropdown_template();
    $t_currencies_dropdown_html = $coo_currencies_dropdown->get_html();
}

$this->set_content_data('CURRENCIES_DROPDOWN', $t_currencies_dropdown_html);

$t_languages_dropdown_html = '';

$activeLanguagesQuery = 'SELECT * FROM `languages` WHERE `status` = 1';

if (xtc_db_query($activeLanguagesQuery)->num_rows > 1) {
    if (!isset($lng) || !is_object($lng)) {
        include_once(DIR_WS_CLASSES . 'language.php');
        $lng = new language;
    }
    
    $coo_languages_dropdown = MainFactory::create_object('LanguagesBoxThemeContentView');
    $coo_languages_dropdown->set_language_dropdown_template();
    $coo_languages_dropdown->set_('coo_language', $lng);
    if (trim($this->request_type) != '') {
        $coo_languages_dropdown->set_('request_type', $this->request_type);
    }
    $t_languages_dropdown_html = $coo_languages_dropdown->get_html();
}

$this->set_content_data('LANGUAGES_DROPDOWN', $t_languages_dropdown_html);

$t_countries_dropdown_html = '';

if (gm_get_conf('SHOW_TOP_COUNTRY_SELECTION') === 'true' && !isset($_SESSION['customer_id'])) {
    $coo_countries_dropdown = MainFactory::create_object('CountriesBoxThemeContentView');
    $coo_countries_dropdown->setLanguageId($_SESSION['languages_id']);
    
    if (isset($_SESSION['customer_country_iso'])) {
        $coo_countries_dropdown->setCustomerCountryIsoCode(MainFactory::create('CustomerCountryIso2',
                                                                               $_SESSION['customer_country_iso']));
    }
    
    $coo_countries_dropdown->setLanguageId($_SESSION['languages_id']);
    $t_countries_dropdown_html = $coo_countries_dropdown->get_html();
}

$this->set_content_data('COUNTRIES_DROPDOWN', $t_countries_dropdown_html);
