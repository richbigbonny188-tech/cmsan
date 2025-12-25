<?php
/* --------------------------------------------------------------
  filter.php 2022-06-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

# shop language id
$t_shop_language_id = (int)($_SESSION['languages_id'] ?? null);
$t_global_filter    = gm_get_conf('GLOBAL_FILTER');
$t_show_filter      = false;

$c_filter_categories_id = false;
if (isset($_GET['feature_categories_id'])) {
    $c_filter_categories_id = $_GET['feature_categories_id'];
} else {
    if (($this->category_id == 0
         && strpos(strtolower(gm_get_env_info("PHP_SELF")),
                   'index.php') !== false)
        || $this->category_id > 0) {
        // startpage, category listing or product details
        $c_filter_categories_id = $this->category_id;
    }
}

if ($c_filter_categories_id !== false) {
    $t_coo_control = MainFactory::create_object('FeatureControl');
    $t_show_filter = $t_coo_control->is_category_filter_enabled($c_filter_categories_id);
    
    if ($c_filter_categories_id == 0 && gm_get_conf('STARTPAGE_FILTER_ACTIVE') == "1") {
        $t_show_filter = true;
    }
}

if ($t_global_filter == true && gm_get_conf('STARTPAGE_FILTER_ACTIVE') == "1") {
    // global filter
    $t_show_filter = true;
}

if (isset($_GET['manufacturers_id'])) {
    $t_show_filter = false;
}

if (!isset($actual_products_id)) {
    $actual_products_id = null;
}
if (($t_show_filter == true || StyleEditServiceFactory::service()->isEditing()) && $actual_products_id == '') {
    $t_selected_feature_value_id_array = [];
    $t_features_values_id              = [];
    
    $t_use_persistent_filter     = gm_get_conf('PERSISTENT_GLOBAL_FILTER');
    
    if (isset($_GET['filter_fv_id']) || isset($_GET['filter_price_min']) || isset($_GET['filter_price_max'])
        || isset($_GET['value_conjunction'])) {
        // clear filter
        $_SESSION['coo_filter_manager']->reset();
    }
    
    $t_feature_value_group_array = $_SESSION['coo_filter_manager']->get_feature_value_group_array();
    
    if (!empty($t_feature_value_group_array) && $t_use_persistent_filter === '1') {
        $t_features_values_id = array_column($t_feature_value_group_array, 'FEATURE_VALUE_ID_ARRAY');
    } elseif (!empty($_GET['filter_fv_id']) && is_array($_GET['filter_fv_id'])) {
        $t_features_values_id = $_GET['filter_fv_id'];
    }
    
    foreach ($t_features_values_id as $features) {
        $t_selected_feature_value_id_array = array_merge($t_selected_feature_value_id_array, array_values($features));
    }
    
    if (isset($_GET['filter_price_min'])) {
        $t_price_start = $_GET['filter_price_min'];
    } else {
        $t_price_start = '';
    }
    if (isset($_GET['filter_price_max'])) {
        $t_price_end = $_GET['filter_price_max'];
    } else {
        $t_price_end = '';
    }
    
    $_SESSION['coo_filter_manager']->set_categories_id($c_filter_categories_id);
    
    $coo_content_view = MainFactory::create_object('FilterBoxThemeContentView');
    $coo_content_view->setCategoryId($c_filter_categories_id);
    $coo_content_view->setLanguageId($t_shop_language_id);
    $coo_content_view->setSelectedValuesArray($t_selected_feature_value_id_array);
    $coo_content_view->setPriceStart($t_price_start);
    $coo_content_view->setPriceEnd($t_price_end);
    $t_html = $coo_content_view->get_html();
    $this->set_content_data($GLOBALS['coo_template_control']->get_menubox_position('filter'), $t_html);
    $this->set_content_data('HAS_FILTERS', true);
}
