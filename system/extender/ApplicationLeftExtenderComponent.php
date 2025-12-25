<?php
/*--------------------------------------------------------------------------------------------------
    ApplicationLeftExtenderComponent.php 2020-07-03
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
MainFactory::load_class('ExtenderComponent');

/**
 * Class ApplicationLeftExtenderComponent
 */
class ApplicationLeftExtenderComponent extends ExtenderComponent
{
    /**
     * @var ApplicationBottomExtenderComponent
     */
    protected $applicationBottomExtender;
    /**
     * @var TemplateControl
     */
    protected $templateControl;
    
    
    /**
     * ApplicationLeftExtenderComponent constructor.
     *
     * @param TemplateControl                    $templateControl
     * @param ApplicationBottomExtenderComponent $applicationBottomExtender
     */
    public function __construct(
        TemplateControl $templateControl,
        ApplicationBottomExtenderComponent $applicationBottomExtender
    ) {
        $this->templateControl           = $templateControl;
        $this->applicationBottomExtender = $applicationBottomExtender;
    }
    
    
    /**
     * @return bool
     */
    protected function showLeftColumn(): bool
    {
        $page                 = $this->applicationBottomExtender->get_page();
        $hideOnIndex          = $this->templateControl->findSettingValueByName('gx-index-full-width');
        $hideOnSearch         = $this->templateControl->findSettingValueByName('gx-advanced-search-result-full-width');
        $hideOnContent        = $this->templateControl->findSettingValueByName('gx-shop-content-full-width');
        $hideOnProductInfo    = $this->templateControl->findSettingValueByName('gx-product-info-full-width');
        $hideOnProductListing = $this->templateControl->findSettingValueByName('gx-product-listing-full-width');
        $hideOnCart           = $this->templateControl->findSettingValueByName('gx-shopping-cart-full-width');
        $hideOnWishlist       = $this->templateControl->findSettingValueByName('gx-wishlist-full-width');
        $hideOnCheckout       = $this->templateControl->findSettingValueByName('gx-checkout-full-width');
        $hideOnAccount        = $this->templateControl->findSettingValueByName('gx-account-full-width');
        $hideOnLogoff         = $this->templateControl->findSettingValueByName('gx-logoff-full-width');
        
        if (($page === PageType::INDEX && $hideOnIndex)
            || ($page === PageType::SEARCH && $hideOnSearch)
            || ($page === PageType::CONTENT && $hideOnContent)
            || ($page === PageType::PRODUCT_INFO && $hideOnProductInfo)
            || ($page === PageType::CAT && $hideOnProductListing)
            || ($page === PageType::CART && $hideOnCart)
            || ($page === PageType::WISH_LIST && $hideOnWishlist)
            || ($page === PageType::CHECKOUT && $hideOnCheckout)
            || ($page === PageType::LOGOFF && $hideOnLogoff)
            || (($page === PageType::ACCOUNT || $page === PageType::ACCOUNT_HISTORY || $page === PageType::ADDRESS_BOOK_PROCESS)
                && $hideOnAccount)) {
            return false;
        }
        
        return true;
    }
    
}