<?php
/* --------------------------------------------------------------
   UpcomingProductsMainThemeContentView.inc.php 2023-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(upcoming_products.php,v 1.23 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (upcoming_products.php,v 1.7 2003/08/22); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: upcoming_products.php 1243 2005-09-25 09:33:02Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_date_short.inc.php');

class UpcomingProductsMainThemeContentView extends ThemeContentView
{
    protected $customers_status_id;
    protected $customers_fsk18_display = 0;
    protected $languages_id;
    protected $upcoming_products_count = 0;
    protected $upcoming_products_hard_limit = 30;
    
    
    function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('home_upcoming_products.html');
        $this->set_flat_assigns(true);
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['customers_status_id']     = ['type' => 'int'];
        $this->validation_rules_array['customers_fsk18_display'] = ['type' => 'int'];
        $this->validation_rules_array['languages_id']            = ['type' => 'int'];
        $this->validation_rules_array['upcoming_products_count'] = ['type' => 'int'];
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'customers_status_id',
                                                                        'languages_id'
                                                                    ]);
        
        if (empty($t_uninitialized_array)) {
            if ($this->upcoming_products_count > 0) {
                $t_upcoming_products_query = $this->build_sql_query();
                $t_result                  = xtc_db_query($t_upcoming_products_query);
                
                if (xtc_db_num_rows($t_result) > 0) {
                    $upcomingProducts = [];
                    
                    while ($t_upcoming = xtc_db_fetch_array($t_result)) {
                        $coo_product        = MainFactory::create_object('product', [$t_upcoming['products_id']]);
                        $upcomingProducts[] = $coo_product->buildDataArray($coo_product->data);
                    }

                    $productListingService                      = StaticGXCoreLoader::getService('ProductListingDisplayService');
                    $productListing                             = $productListingService->getUpcomingProducts($upcomingProducts);
                    $this->content_array['module_content']      = $productListing;
                    $this->content_array['module_content_html'] = $this->generateUpcomingProductsListing($productListing);
                }
            } else {
                $this->build_html = false;
            }
        } else {
            trigger_error("Variable(s) " . implode(', ',
                                                   $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    protected function build_sql_query()
    {
        $t_fsk_lock = '';
        if ($this->customers_fsk18_display == 0) {
            $t_fsk_lock = ' AND p.products_fsk18 != 1 ';
        }

        $t_group_check = '';
        if (GROUP_CHECK == 'true') {
            $t_group_check = " AND p.group_permission_" . $this->customers_status_id . " = 1 ";
        }
        
        $t_upcoming_products_query = "SELECT
										p.products_id,
										pd.products_name,
										products_date_available as date_expected
									FROM
										" . TABLE_PRODUCTS . " p,
										" . TABLE_PRODUCTS_DESCRIPTION . " pd
									WHERE
										p.products_status = 1
										AND
										products_date_available > NOW()
										AND p.products_id = pd.products_id
										" . $t_group_check . "
										" . $t_fsk_lock . "
										AND pd.language_id = '" . $this->languages_id . "'
									ORDER BY
										" . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . "
									LIMIT " . min($this->upcoming_products_count, $this->upcoming_products_hard_limit);
        
        return $t_upcoming_products_query;
    }
    
    
    protected function generateUpcomingProductsListing($products)
    {
        $template = 'product_listing.html';
        if (gm_get_conf('USE_UPCOMING_PRODUCT_SWIPER_ON_INDEX') === 'true') {
            $template = 'product_listing_swiper.html';
        }
        
        $showRating = false;
        if (gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true') {
            $showRating = true;
        }
        
        $showManufacturerImages = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
        $showProductRibbons     = gm_get_conf('SHOW_PRODUCT_RIBBONS');
        
        $fullscreenPage = $GLOBALS['coo_template_control']->findSettingValueByName('gx-index-full-width');
        
        $swiperData = [
            'products'               => $products,
            'id'                     => 'upcoming-products',
            'hoverable'              => true,
            'template'               => $template,
            'truncate'               => gm_get_conf('TRUNCATE_PRODUCTS_NAME'),
            'showRating'             => $showRating,
            'fullscreenPage'         => $fullscreenPage,
            'showManufacturerImages' => $showManufacturerImages,
            'showProductRibbons'     => $showProductRibbons,
        ];
        
        $swiperHtml = MainFactory::create_object('ProductsSwiperThemeContentView', [$swiperData]);
        
        return $swiperHtml->get_html();
    }
}
