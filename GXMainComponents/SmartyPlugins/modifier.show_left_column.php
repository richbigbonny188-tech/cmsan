<?php
/* --------------------------------------------------------------
   modifier.show_left_column.php 2020-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_modifier_show_left_column($string)
{
    $applicationBottomExtenderComponent = MainFactory::create_object('ApplicationBottomExtenderComponent');
    $applicationBottomExtenderComponent->set_data('GET', $_GET);
    $applicationBottomExtenderComponent->init_page();
    $page = $applicationBottomExtenderComponent->get_page();
    
    $hideOnIndex          = $GLOBALS['coo_template_control']->findSettingValueByName('gx-index-full-width');
    $hideOnSearch         = $GLOBALS['coo_template_control']->findSettingValueByName('gx-advanced-search-result-full-width');
    $hideOnContent        = $GLOBALS['coo_template_control']->findSettingValueByName('gx-shop-content-full-width');
    $hideOnProductInfo    = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-info-full-width');
    $hideOnProductListing = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-full-width');
    $hideOnCart           = $GLOBALS['coo_template_control']->findSettingValueByName('gx-shopping-cart-full-width');
    $hideOnWishlist       = $GLOBALS['coo_template_control']->findSettingValueByName('gx-wishlist-full-width');
    $hideOnCheckout       = $GLOBALS['coo_template_control']->findSettingValueByName('gx-checkout-full-width');
    $hideOnAccount        = $GLOBALS['coo_template_control']->findSettingValueByName('gx-account-full-width');
    $hideOnLogoff         = $GLOBALS['coo_template_control']->findSettingValueByName('gx-logoff-full-width');

    if (($page === PageType::INDEX && $hideOnIndex)
        || ($page === PageType::SEARCH && $hideOnSearch)
        || ($page === PageType::CONTENT && $hideOnContent)
        || ($page === PageType::PRODUCT_INFO && $hideOnProductInfo)
        || ($page === PageType::CAT && $hideOnProductListing)
        || ($page === PageType::CART && $hideOnCart)
        || ($page === PageType::WISH_LIST && $hideOnWishlist)
        || ($page === PageType::CHECKOUT && $hideOnCheckout)
        || ($page === PageType::LOGOFF && $hideOnLogoff)
        || (($page === PageType::ACCOUNT || $page === PageType::ACCOUNT_HISTORY || $page === PageType::ADDRESS_BOOK_PROCESS) && $hideOnAccount)) {
        return false;
    }
    
    return true;
}