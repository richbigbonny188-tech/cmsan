<?php
/* --------------------------------------------------------------
   boxes.php 2023-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: boxes.php 1298 2005-10-09 13:14:44Z mz $)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

if (!defined('DIR_WS_BOXES')) {
    define('DIR_WS_BOXES', DIR_FS_CATALOG . 'GXMainComponents/View/Boxes/boxes/');
}

include(DIR_WS_BOXES . 'content_top.php');
include(DIR_WS_BOXES . 'extraboxes.php');
include(DIR_WS_BOXES . 'gm_logo.php');
include(DIR_WS_BOXES . 'slider.php');
include(DIR_WS_BOXES . 'top_menu.php');

if (gm_get_conf('CAT_MENU_TOP') == 'true') {
    include(DIR_WS_BOXES . 'megadropdown.php');
}
if (gm_get_conf('GM_QUICK_SEARCH') == 'true') {
    include(DIR_WS_BOXES . 'top_search.php');
}
$footer = gm_get_content('GM_FOOTER', $_SESSION['languages_id']) ? : gm_get_conf('GM_FOOTER');
if (gm_get_conf('SHOW_FOOTER') != 'true') {
    $this->set_content_data('COPYRIGHT_FOOTER', $footer);
}

# $coo_template_control created in application_top.php
if ($GLOBALS['coo_template_control']->get_menubox_status('categories')) {
    include(DIR_WS_BOXES . 'categories.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('content')) {
    include(DIR_WS_BOXES . 'content.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('filter')) {
    include(DIR_WS_BOXES . 'filter.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('information')) {
    include(DIR_WS_BOXES . 'information.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('last_viewed')) {
    include(DIR_WS_BOXES . 'last_viewed.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('login')) {
    include(DIR_WS_BOXES . 'loginbox.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('manufacturers')) {
    include(DIR_WS_BOXES . 'manufacturers.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('newsletter')) {
    include(DIR_WS_BOXES . 'newsletter.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('paypal')) {
    include(DIR_WS_BOXES . 'paypal.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('paypalinstallments')) {
    include(DIR_WS_BOXES . 'paypalinstallments.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('search')) {
    include(DIR_WS_BOXES . 'search.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('trusted')) {
    include(DIR_WS_BOXES . 'trusted.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('ekomi')) {
    include(DIR_WS_BOXES . 'ekomi.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('mailbeez_shopvoting')) {
    include(DIR_WS_BOXES . 'mailbeez_shopvoting.php');
}

if ($GLOBALS['coo_template_control']->get_menubox_status('admin')
    && $_SESSION['customers_status']['customers_status_id'] === '0'
    && (int)$_SESSION['customer_id'] > 0) {
    include(DIR_WS_BOXES . 'admin.php');
}
if ($GLOBALS['coo_template_control']->get_menubox_status('add_quickie')) {
    if (StyleEditServiceFactory::service()->isEditing()
        || $_SESSION['customers_status']['customers_status_show_price'] != '0') {
        include(DIR_WS_BOXES . 'add_a_quickie.php');
    }
}
if ($GLOBALS['coo_template_control']->get_menubox_status('bestsellers')) {
    if (StyleEditServiceFactory::service()->isEditing() || !$this->coo_product->isProduct()) {
        include(DIR_WS_BOXES . 'best_sellers.php');
    }
}
if ($GLOBALS['coo_template_control']->get_menubox_status('manufacturers_info')) {
    if (StyleEditServiceFactory::service()->isEditing() || $this->coo_product->isProduct()) {
        include(DIR_WS_BOXES . 'manufacturer_info.php');
    }
}
if ($GLOBALS['coo_template_control']->get_menubox_status('order_history')) {
    if (StyleEditServiceFactory::service()->isEditing() || isset($_SESSION['customer_id'])) {
        include(DIR_WS_BOXES . 'order_history.php');
    }
}
if ($GLOBALS['coo_template_control']->get_menubox_status('specials')) {
    if (StyleEditServiceFactory::service()->isEditing() || !$this->coo_product->isProduct()) {
        include(DIR_WS_BOXES . 'specials.php');
    }
}
if ($GLOBALS['coo_template_control']->get_menubox_status('whatsnew')) {
    if (StyleEditServiceFactory::service()->isEditing() || substr(basename(gm_get_env_info('PHP_SELF')), 0, 8) !== 'advanced') {
        include(DIR_WS_BOXES . 'whats_new.php');
    }
}

$this->set_content_data('tpl_path', DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeHtmlPath());
