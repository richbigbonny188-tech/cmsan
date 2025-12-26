<?php
/* --------------------------------------------------------------
  index.php 2022-08-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
  (c) 2003	 nextcommerce (default.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: index.php 1321 2005-10-26 20:55:07Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

include('includes/application_top.php');

if (($_GET['gm_boosted_category'] ?? null) == ''
    && (
    
        (gm_get_conf('SUPPRESS_INDEX_IN_URL') !== 'true' && strpos($_SERVER['REQUEST_URI'], 'index.php') === false)
        || (gm_get_conf('SUPPRESS_INDEX_IN_URL') === 'true'
            && strpos($_SERVER['REQUEST_URI'], 'index.php') !== false))) {
    header("HTTP/1.1 301 Moved Permanently");
    header('Location:' . xtc_href_link(FILENAME_DEFAULT,
                                       xtc_get_all_get_params(['gm_boosted_category']),
                                       'NONSSL',
                                       true,
                                       true,
                                       false,
                                       false));
    exit;
}
$applicationBottomExtenderComponent = MainFactory::create_object('ApplicationBottomExtenderComponent');
$applicationBottomExtenderComponent->set_data('GET', $_GET);
$applicationBottomExtenderComponent->init_page();

$coo_mn_menu_boxes_control = MainFactory::create_object('MenuBoxesContentControl',
                                                        [
                                                            $GLOBALS['coo_template_control'],
                                                            $applicationBottomExtenderComponent->get_page()
                                                        ]);

$coo_mn_menu_boxes_control->set_('account_type', $_SESSION['account_type'] == '0' ? '0' : null);
$coo_mn_menu_boxes_control->set_('c_path', $GLOBALS['cPath']);
$coo_mn_menu_boxes_control->set_('category_id', isset($GLOBALS['cID']) ? (int)$GLOBALS['cID'] : 0);
$coo_mn_menu_boxes_control->set_('customer_id', $_SESSION['customer_id'] ?? null);
$coo_mn_menu_boxes_control->set_('request_type', $GLOBALS['request_type']);
$coo_mn_menu_boxes_control->set_('coo_product', $GLOBALS['product']);
$coo_mn_menu_boxes_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
$coo_mn_menu_boxes_control->proceed();

$coo_listing_control = MainFactory::create_object('ProductListingContentControl');
$coo_listing_control->set_data('GET', $_GET);
$coo_listing_control->set_data('POST', $_POST);
$coo_listing_control->set_('coo_mn_data_container', $coo_mn_menu_boxes_control);
$coo_listing_control->set_('c_path', $GLOBALS['cPath']);
$coo_listing_control->set_('cat', $_GET['cat'] ?? null);
$coo_listing_control->set_('categories_id', isset($GLOBALS['cID']) ? (int)$GLOBALS['cID'] : 0);
$coo_listing_control->set_('coo_filter_manager', $_SESSION['coo_filter_manager']);
$coo_listing_control->set_('coo_product', $GLOBALS['product']);
$coo_listing_control->set_('currency_code', $_SESSION['currency']);
$coo_listing_control->set_('current_category_id', (int)$GLOBALS['current_category_id']);
$coo_listing_control->set_('current_page', basename($GLOBALS['PHP_SELF']));

$country = isset($_GET['customer_country_id']) ? $_SESSION['customer_country_id'] : STORE_COUNTRY;
$zone    = isset($_GET['customer_zone_id']) ? $_SESSION['customer_zone_id'] : STORE_ZONE;
$filter_fv_id = $_GET['filter_fv_id'] ?? null;

if ($filter_fv_id !== null) {
    $filter_fv_id = is_array($filter_fv_id) ? $filter_fv_id : (int)$filter_fv_id;
}

$coo_listing_control->set_('customer_country_id', (int)$country);
$coo_listing_control->set_('customer_zone_id', (int)$zone);
$coo_listing_control->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);
$coo_listing_control->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
$coo_listing_control->set_('filter_fv_id', $filter_fv_id);
$coo_listing_control->set_('filter_id', isset($_GET['filter_id']) ? (int)$_GET['filter_id'] : null);
$coo_listing_control->set_('filter_price_min', $_GET['filter_price_min'] ?? null);
$coo_listing_control->set_('filter_price_max', $_GET['filter_price_max'] ?? null);
$coo_listing_control->set_('feature_categories_id', isset($_GET['feature_categories_id']) ? (int)$_GET['feature_categories_id'] : null);
$coo_listing_control->set_('show_graduated_prices',
                           !empty($_SESSION['customers_status']['customers_status_graduated_prices']));
$coo_listing_control->set_('languages_id', (int)($_SESSION['languages_id'] ?? null));

if (!isset($_SESSION['last_listing_sql'])) {
    $_SESSION['last_listing_sql'] = '';
}

$coo_listing_control->reference_set_('last_listing_sql', $_SESSION['last_listing_sql']);
$coo_listing_control->set_('listing_count', isset($_GET['listing_count']) ? (int)$_GET['listing_count'] : null);
$coo_listing_control->set_('listing_sort', $_GET['listing_sort'] ?? null);

if (!empty($_GET['manufacturers_id'])) {
    $coo_listing_control->set_('manufacturers_id', (int)$_GET['manufacturers_id']);
}

if (!empty($_GET['page'])) {
    $coo_listing_control->set_('page_number', (int)$_GET['page']);
}
$coo_listing_control->set_('sort', $_GET['sort'] ?? null);
$coo_listing_control->set_('value_conjunction', isset($_GET['value_conjunction']) ? (array)$_GET['value_conjunction'] : null);
$coo_listing_control->set_('view_mode', isset($_GET['view_mode']) ? (string)$_GET['view_mode'] : null);
$coo_listing_control->set_('show_price_tax', $_SESSION['customers_status']['customers_status_show_price_tax']);
$coo_listing_control->proceed();

$t_redirect_url = $coo_listing_control->get_redirect_url();
if (empty($t_redirect_url) === false) {
    xtc_redirect($t_redirect_url);
} else {
    $t_main_content = $coo_listing_control->get_response();
}

/** @var LayoutContentControl $coo_layout_control */
$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_('coo_mn_data_container', $coo_mn_menu_boxes_control);
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
$coo_layout_control->set_('category_id', $GLOBALS['cID'] ?? 0);
$coo_layout_control->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
$coo_layout_control->set_('coo_product', $GLOBALS['product']);
$coo_layout_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
$coo_layout_control->set_('c_path', $GLOBALS['cPath']);
$coo_layout_control->set_('main_content', $t_main_content);
$coo_layout_control->set_('request_type', $GLOBALS['request_type']);
$coo_layout_control->proceed();

$t_redirect_url = $coo_layout_control->get_redirect_url();
if (empty($t_redirect_url) === false) {
    xtc_redirect($t_redirect_url);
} else {
    echo $coo_layout_control->get_response();
}

