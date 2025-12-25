<?php
/* --------------------------------------------------------------
  advanced_search_result.php 2021-07-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(advanced_search_result.php,v 1.68 2003/05/14); www.oscommerce.com
  (c) 2003	 nextcommerce (advanced_search_result.php,v 1.17 2003/08/21); www.nextcommerce.org
  (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: advanced_search_result.php 1141 2005-08-10 11:31:36Z novalis $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

// bof gm
$_GET['keywords']   = htmlspecialchars_wrapper($_GET['keywords'] ?? '', false, '', false);
$_GET['pfrom']      = htmlspecialchars_wrapper($_GET['pfrom'] ?? '');
$_GET['pto']        = htmlspecialchars_wrapper($_GET['pto'] ?? '');
$_GET['inc_subcat'] = isset($_GET['inc_subcat']) && is_numeric($_GET['inc_subcat']) ? (int)$_GET['inc_subcat'] : 0;

if (isset($_GET['filter_price_min'])) {
    $_GET['filter_price_min'] = htmlspecialchars_wrapper($_GET['filter_price_min']);
}
if (isset($_GET['filter_price_max'])) {
    $_GET['filter_price_max'] = htmlspecialchars_wrapper($_GET['filter_price_max']);
}
if (isset($_GET['filter_url'])) {
    $_GET['filter_url'] = htmlspecialchars_wrapper($_GET['filter_url']);
}
if (isset($_GET['keywords'])) {
    $_GET['keywords'] = htmlspecialchars_wrapper($_GET['keywords']);
}
if (isset($_GET['categories_id'])) {
    $_GET['categories_id'] = (int)$_GET['categories_id'];
}
if (isset($_GET['filter_categories_id'])) {
    $_GET['filter_categories_id'] = (int)$_GET['filter_categories_id'];
}

function recursiveIntegerValue($p_variable)
{
    if (is_array($p_variable)) {
        $c_variable = [];
        
        foreach ($p_variable as $t_key => $t_value) {
            $c_variable[$t_key] = '';
            if ($t_value !== '') {
                $c_variable[$t_key] = recursiveIntegerValue($t_value);
            }
        }
    } else {
        $c_variable = (string)(int)$p_variable;
    }
    
    return $c_variable;
}

function filterIds($p_variable)
{
    if (is_array($p_variable)) {
        $c_variable = [];
        
        foreach ($p_variable as $t_key => $t_value) {
            $c_key              = (string)(int)$t_key;
            $c_variable[$c_key] = '';
            if ($t_value !== '') {
                $c_variable[$c_key] = recursiveIntegerValue($t_value);
            }
        }
    } else {
        $c_variable = preg_replace('/[^0-9&:\|]/', '', (string)$p_variable);
    }
    
    return $c_variable;
}

if (isset($_GET["filter_fv_id"])) {
    $_GET["filter_fv_id"] = filterIds($_GET["filter_fv_id"]);
}

if (isset($_GET["value_conjunction"])) {
    $_GET["value_conjunction"] = filterIds($_GET["value_conjunction"]);
}
// eof gm

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE1_ADVANCED_SEARCH, xtc_href_link(FILENAME_ADVANCED_SEARCH));
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE2_ADVANCED_SEARCH);

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
$coo_mn_menu_boxes_control->set_('category_id', $GLOBALS['cID'] ?? 0);
$coo_mn_menu_boxes_control->set_('customer_id', $_SESSION['customer_id'] ?? null);
$coo_mn_menu_boxes_control->set_('request_type', $GLOBALS['request_type']);
$coo_mn_menu_boxes_control->set_('coo_product', $GLOBALS['product']);
$coo_mn_menu_boxes_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
$coo_mn_menu_boxes_control->proceed();

$coo_listing_control = MainFactory::create_object('ProductListingContentControl');
$coo_listing_control->set_('coo_mn_data_container', $coo_mn_menu_boxes_control);

$coo_listing_control->set_data('GET', $_GET);
$coo_listing_control->set_data('POST', $_POST);

// BEGIN Findologic
$use_findologic = gm_get_conf('FL_USE_SEARCH') == true;
$fl_unavailable = isset($_GET['fl_unavailable']);
if ($use_findologic && !$fl_unavailable) {
    $coo_flcontrol        = MainFactory::create_object('FindologicControl', []);
    $do_findologic_search = $coo_flcontrol->is_alive(FL_SERVICE_URL);
    if (isset($_GET['fallback']) && $_GET['fallback'] == 1) {
        $do_findologic_search = false;
    }
    
    if ($do_findologic_search) {
        $t_fl_get          = $_GET;
        $t_fl_searchresult = $coo_flcontrol->get_search_result($t_fl_get);
        
        if ($t_fl_searchresult['success'] !== true) {
            $do_findologic_search = false;
        } else {
            if ($t_fl_searchresult['forward_url'] !== false) {
                xtc_redirect($t_fl_searchresult['forward_url']);
            }
            $coo_listing_control->set_('product_ids', $t_fl_searchresult['product_ids']);
        }
    } else {
        $t_get_params   = array_merge($_GET, ['fl_unavailable' => '1']);
        $t_fallback_url = GM_HTTP_SERVER . DIR_WS_CATALOG . basename(__FILE__) . '?' . http_build_query($t_get_params);
        xtc_redirect($t_fallback_url);
    }
}
// END Findologic

if (isset($_GET['categories_id']) && empty($_GET['categories_id']) == false) {
    $coo_listing_control->set_('categories_id', $_GET['categories_id']);
}

$coo_listing_control->set_('coo_filter_manager', $_SESSION['coo_filter_manager']);
$coo_listing_control->set_('coo_product', $GLOBALS['product']);
$coo_listing_control->set_('currency_code', $_SESSION['currency']);
$coo_listing_control->set_('current_category_id', $GLOBALS['current_category_id']);
$coo_listing_control->set_('current_page', basename($GLOBALS['PHP_SELF']));

if (isset($_GET['customer_country_id'])) {
    $coo_listing_control->set_('customer_country_id', $_SESSION['customer_country_id']);
} else {
    $coo_listing_control->set_('customer_country_id', STORE_COUNTRY);
}

if (isset($_GET['customer_zone_id'])) {
    $coo_listing_control->set_('customer_zone_id', $_SESSION['customer_zone_id']);
} else {
    $coo_listing_control->set_('customer_zone_id', STORE_ZONE);
}

$coo_listing_control->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);
$coo_listing_control->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);

if (isset($_GET['inc_subcat'])) {
    $coo_listing_control->set_('include_subcategories_for_search', $_GET['inc_subcat']);
}

$coo_listing_control->set_('languages_id', $_SESSION['languages_id']);

if (isset($_GET['listing_count'])) {
    $coo_listing_control->set_('listing_count', isset($_GET['listing_count']) ? (int)$_GET['listing_count'] : null);
}

if (isset($_GET['listing_sort'])) {
    $coo_listing_control->set_('listing_sort', $_GET['listing_sort']);
}

if (isset($_GET['manufacturers_id']) && empty($_GET['manufacturers_id']) == false) {
    $coo_listing_control->set_('manufacturers_id', $_GET['manufacturers_id']);
}

if (isset($_GET['page'])) {
    $coo_listing_control->set_('page_number', (int)$_GET['page']);
}

if (isset($_GET['pfrom'])) {
    $coo_listing_control->set_('price_from', $_GET['pfrom']);
}

if (isset($_GET['pto'])) {
    $coo_listing_control->set_('price_to', $_GET['pto']);
}

if (isset($_GET['view_mode'])) {
    $coo_listing_control->set_('view_mode', $_GET['view_mode']);
}

if (empty($_SESSION['customers_status']['customers_status_graduated_prices']) == false) {
    $coo_listing_control->set_('show_graduated_prices', true);
} else {
    $coo_listing_control->set_('show_graduated_prices', false);
}

$coo_listing_control->set_('search_keywords', htmlspecialchars_decode($_GET['keywords']));
$coo_listing_control->set_('show_price_tax', $_SESSION['customers_status']['customers_status_show_price_tax']);

$coo_listing_control->proceed('search_result');

$t_redirect_url = $coo_listing_control->get_redirect_url();
if (empty($t_redirect_url) === false) {
    xtc_redirect($t_redirect_url);
} else {
    $t_main_content = $coo_listing_control->get_response();
}

// BEGIN Findologic
if (isset($t_fl_searchresult) && $t_fl_searchresult['success'] == true) {
    $t_hide_fl_blocks_css = '<style>#flResults,#flPaginator { display: none;} </style>';
    $t_main_content       = $t_fl_searchresult['content_all'] . $t_hide_fl_blocks_css . $t_main_content
                            . $t_fl_searchresult['bottom_content'];
}
// END Findologic

$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
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
