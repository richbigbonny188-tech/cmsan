<?php
/* --------------------------------------------------------------
  LayoutThemeContentView.inc.php 2019-06-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

// include needed functions
require_once(DIR_FS_INC . 'xtc_banner_exists.inc.php');
require_once(DIR_FS_INC . 'xtc_update_banner_display_count.inc.php');

class LayoutThemeContentView extends ThemeContentView
{
    protected $account_type;
    protected $bottom_content;
    protected $c_path;
    protected $category_id;
    protected $coo_breadcrumb;
    protected $coo_product;
    protected $coo_xtc_price;
    protected $customer_id;
    protected $error_message;
    protected $head_content;
    protected $info_message;
    protected $main_content;
    protected $popup_notification_content;
    protected $request_type;
    protected $topbar_content;
    protected $cookiebar_content;
    protected $footer_content;
    protected $coo_mn_data_container;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('index.html');
        $this->set_flat_assigns(true);
    }
    
    
    public function prepare_data()
    {
        $this->set_content_data('HEAD', $this->head_content);
        $this->set_content_data('TOPBAR', $this->topbar_content);
        $this->set_content_data('COOKIEBAR', $this->cookiebar_content);
        $this->set_content_data('POPUP_NOTIFICATION', $this->popup_notification_content);
        $this->set_content_data('FOOTER', $this->footer_content);
        
        $this->assignSecondaryNavigation();
        $this->assignShoppingCart();
        $this->assignFooter();
        
        $this->prepare_menu_boxes();
        
        $this->set_content_data('navtrail', $this->coo_breadcrumb->trail(' &raquo; '));
        $this->assignBanner();
        
        $this->set_content_data('main_content', $this->main_content);
        $this->set_content_data('BOTTOM', $this->bottom_content);
        
        $trustedShopsReviewSticker = '';
        $isInstalled               = gm_get_conf('MODULE_CENTER_TRUSTEDSHOPS_INSTALLED');
        
        if ($isInstalled) {
            $trustedShopsService = MainFactory::create_object('GMTSService');
            $tsid                = $trustedShopsService->findRatingID($_SESSION['language_code']);
            if ($tsid !== false) {
                $reviewSnippet = $trustedShopsService->getReviewStickerSnippet($tsid);
                if ($reviewSnippet['enabled'] == true) {
                    $trustedShopsReviewSticker .= $reviewSnippet['snippet_code'];
                }
                
                $appendRichSnippet = false;
                if ((int)$this->category_id > 0 && $this->coo_product->isProduct == false
                    && $trustedShopsService->richsnippets_enabled_categories == true) {
                    // category page
                    $appendRichSnippet = true;
                } else {
                    if ((int)$this->category_id == 0 && $this->coo_product->isProduct == true
                        && $trustedShopsService->richsnippets_enabled_products == true) {
                        // product page
                        $appendRichSnippet = true;
                    } else {
                        if ($trustedShopsService->richsnippets_enabled_other == true) {
                            // other page
                            $appendRichSnippet = true;
                        }
                    }
                }
                if ($appendRichSnippet == true) {
                    $trustedShopsReviewSticker .= $trustedShopsService->getRichSnippet($tsid);
                }
            }
        }
        
        $this->set_content_data('TRUSTED_SHOPS_REVIEW_STICKER', $trustedShopsReviewSticker);
    }
    
    
    public function assignSecondaryNavigation()
    {
        $coo_top_navigation = MainFactory::create_object('TopNavigationBoxThemeContentView');
        $coo_top_navigation->setXtcPrice($this->coo_xtc_price);
        $coo_top_navigation->prepare_data();
        $t_top_navigation_content_array = $coo_top_navigation->get_content_array();
        foreach ($t_top_navigation_content_array as $data => $value) {
            $this->set_content_data($data, $value);
        }
    }
    
    
    public function assignShoppingCart()
    {
        $coo_shopping_cart_content_view = MainFactory::create_object('ShoppingCartDropdownBoxThemeContentView');
        $coo_shopping_cart_content_view->set_('coo_cart', $_SESSION['cart']);
        $coo_shopping_cart_content_view->set_('language_id', $_SESSION['languages_id']);
        $coo_shopping_cart_content_view->set_('language_code', $_SESSION['language_code']);
        $coo_shopping_cart_content_view->set_('customers_status_ot_discount_flag',$_SESSION['customers_status']['customers_status_ot_discount_flag']);
        $coo_shopping_cart_content_view->set_('customers_status_ot_discount',$_SESSION['customers_status']['customers_status_ot_discount']);
        $coo_shopping_cart_content_view->set_('customers_status_show_price_tax',$_SESSION['customers_status']['customers_status_show_price_tax']);
        $coo_shopping_cart_content_view->set_('customers_status_add_tax_ot',$_SESSION['customers_status']['customers_status_add_tax_ot']);
        $coo_shopping_cart_content_view->set_('customers_status_show_price',$_SESSION['customers_status']['customers_status_show_price']);
        $coo_shopping_cart_content_view->set_('customers_status_payment_unallowed',$_SESSION['customers_status']['customers_status_payment_unallowed']);
        $coo_shopping_cart_content_view->prepare_data();
        $coo_shopping_cart_content_array = $coo_shopping_cart_content_view->get_content_array();
        foreach ($coo_shopping_cart_content_array as $data => $value) {
            $this->set_content_data($data, $value);
        }
    }
    
    
    public function assignFooter()
    {
        $view = MainFactory::create_object('FooterThemeContentView');
        $view->set_flat_assigns(true);
        $view->set_('language_id', $_SESSION['languages_id']);
        $view->set_('customer_status_id', $_SESSION['customers_status']['customers_status_id']);
        $view->set_('footerColumns', ['4321005', '4321006', '4321007']);
        $view->prepare_data();
        $footerContentArray = $view->get_content_array();
        
        foreach ($footerContentArray as $data => $value) {
            $this->set_content_data($data, $value);
        }
    }
    
    
    public function assignBanner()
    {
        $banner = xtc_banner_exists('dynamic', 'banner');
        if (is_array($banner)) {
            if (xtc_not_null($banner['banners_html_text'])) {
                $this->set_content_data('BANNER_HTML', $banner['banners_html_text']);
            } else {
                $this->set_content_data('BANNER_LINK',
                                        xtc_href_link(FILENAME_REDIRECT,
                                                      'action=banner&goto=' . $banner['banners_id']));
                $this->set_content_data('BANNER_IMAGE', DIR_WS_IMAGES . 'banner/' . $banner['banners_image']);
                $this->set_content_data('BANNER_ALT', $banner['banners_title']);
            }
            
            xtc_update_banner_display_count($banner['banners_id']);
        }
    }
    
    
    /**
     * @return string
     * @deprecated
     */
    public function get_banner()
    {
        // deprecated
        return '';
    }
    
    
    protected function set_validation_rules()
    {
        // GENERAL VALIDATION RULES
        $this->validation_rules_array['account_type']      = ['type' => 'int'];
        $this->validation_rules_array['bottom_content']    = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['c_path']            = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['category_id']       = ['type' => 'int'];
        $this->validation_rules_array['coo_breadcrumb']    = ['type' => 'object', 'object_type' => 'breadcrumb'];
        $this->validation_rules_array['coo_product']       = ['type' => 'object', 'object_type' => 'product'];
        $this->validation_rules_array['coo_xtc_price']     = ['type' => 'object', 'object_type' => 'xtcPrice'];
        $this->validation_rules_array['customer_id']       = ['type' => 'int'];
        $this->validation_rules_array['error_message']     = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['head_content']      = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['info_message']      = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['main_content']      = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['request_type']      = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['topbar_content']    = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['cookiebar_content'] = ['type' => 'string', 'strict' => 'true'];
        $this->validation_rules_array['footer_content']    = ['type' => 'string', 'strict' => 'true'];
        
        $this->validation_rules_array['popup_notification_content'] = ['type' => 'string', 'strict' => 'true'];
    }
    
    protected function prepare_menu_boxes(): void
    {
        if ($this->coo_mn_data_container) {
            foreach ($this->coo_mn_data_container->MenuBoxData() as $key => $value) {
                $this->set_content_data($key, $value);
            }
        }
    }
}
