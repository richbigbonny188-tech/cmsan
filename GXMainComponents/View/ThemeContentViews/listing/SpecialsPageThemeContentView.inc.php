<?php
/* --------------------------------------------------------------
   SpecialsPageThemeContentView.inc.php 2024-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(specials.php,v 1.47 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (specials.php,v 1.12 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: specials.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_get_short_description.inc.php');

class SpecialsPageThemeContentView extends ThemeContentView
{
    protected $redirect = false;
    protected $coo_product;
    protected $language_id;
    protected $currency;
    protected $customer_status_id;
    protected $page;
    protected $coo_cache;

    /**
     * @var ExtendedInformationPager
     */
    protected $pager;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('offers.html');
        $this->set_flat_assigns(true);
    }
    
    
    protected function set_validation_rules()
    {
        // SET VALIDATION RULES
        $this->validation_rules_array['redirect']           = ['type' => 'bool'];
        $this->validation_rules_array['coo_product']        = [
            'type'        => 'object',
            'object_type' => 'product'
        ];
        $this->validation_rules_array['language_id']        = ['type' => 'int'];
        $this->validation_rules_array['currency']           = ['type' => 'string'];
        $this->validation_rules_array['customer_status_id'] = ['type' => 'int'];
        $this->validation_rules_array['page']               = ['type' => 'int'];
        $this->validation_rules_array['coo_cache']          = [
            'type'        => 'object',
            'object_type' => 'DataCache'
        ];
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'language_id',
                                                                        'currency',
                                                                        'customer_status_id',
                                                                        'page'
                                                                    ]);
        if (empty($t_uninitialized_array)) {
            if (xtc_not_null(SID)) {
                $t_use_sid = 'sid_TRUE';
            } else {
                $t_use_sid = 'sid_FALSE';
            }
            
            // parameter list for cache matching
            $t_cache_key_source = 'specials-' . (int)$this->page . '-' . $this->language_id . '-' . $this->currency
                                  . '-' . $this->customer_status_id . '-' . $t_use_sid;
            
            $this->coo_cache = DataCache::get_instance();
            $t_cache_key     = $this->coo_cache->build_key($t_cache_key_source);
            
            $this->get_data($t_cache_key);
            
            $this->content_array['pager']              = $this->pager;
            $this->content_array['GM_THUMBNAIL_WIDTH'] = PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10;
        } else {
            trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
        
        $this->content_array['showManufacturerImages'] = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
        $this->content_array['showProductRibbons']     = gm_get_conf('SHOW_PRODUCT_RIBBONS');

        if ($this->pager->totalPageCount() > 1) {
            $navigationUrl = splitPageResults::get_navigation_url();
            $pageParam = $this->pager->pageParameter();
            $pageParam = strpos($navigationUrl, '?') !== false ? "&{$pageParam}=" : "?{$pageParam}=";

            $this->set_content_data('pages', $this->pager->getPages());
            $this->set_content_data('navigation_url', $navigationUrl);
            $this->set_content_data('page_param', $pageParam);
        }

        $showRating = false;
        if (gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true') {
            $showRating = true;
        }
        $this->content_array['showRating'] = $showRating;
    }
    
    
    protected function get_data($p_cache_key)
    {
        $t_data_cache_exists = $this->check_cache($p_cache_key);
        if ($t_data_cache_exists) {
            $this->get_data_from_cache($p_cache_key);
        } else {
            $this->generate_data();
            $this->save_data_to_cache($p_cache_key);
        }
    }
    
    
    protected function check_cache($p_cache_key)
    {
        if ($this->coo_cache->key_exists($p_cache_key, true)) {
            return true;
        }
        
        return false;
    }
    
    
    protected function get_data_from_cache($p_cache_key)
    {
        // use cached result
        $t_cache_data_array                    = $this->coo_cache->get_data($p_cache_key);
        $this->content_array['module_content'] = $t_cache_data_array['module_content'];
        $this->pager                           = $t_cache_data_array['pager'];
    }
    
    
    protected function generate_data()
    {
        //fsk18 lock
        $fsk_lock = '';
        if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
            $fsk_lock = ' and p.products_fsk18!=1';
        }
        if (GROUP_CHECK == 'true') {
            $group_check = " and p.group_permission_" . $this->customer_status_id . "=1 ";
        }
        
        $t_query = "SELECT
            `p`.`products_id`
        FROM
            ( SELECT
                  `s`.`products_id`,
                  `s`.`specials_date_added`
               FROM
                  " . TABLE_SPECIALS . " `s`
               WHERE
                  `s`.`status` = '1') AS `s`,
            " . TABLE_PRODUCTS . " `p`,
            " . TABLE_PRODUCTS_DESCRIPTION . " `pd`
        WHERE
            `p`.`products_id` = `s`.`products_id`
            AND `p`.`products_status` = '1'
            AND `p`.`products_id` = `pd`.`products_id`
            " . ($group_check ?? null) . "
            " . $fsk_lock . "
               AND pd.language_id = '" . $this->language_id . "'
        ORDER BY s.specials_date_added DESC";
        
        $this->pager = $this->createPager($this->page, $t_query, MAX_DISPLAY_SPECIAL_PRODUCTS);

        $t_query .= " LIMIT {$this->pager->offset()}, {$this->pager->perPage()}";
        $specials_query = xtc_db_query($t_query);
        $this->content_array['module_content'] = [];
        
        if(xtc_db_num_rows($specials_query) > 0) {
            $specialProducts = [];
            while ($specials = xtc_db_fetch_array($specials_query)) {
                $this->coo_product = MainFactory::create_object('product', [$specials['products_id']]);
                $specialProducts[] = $this->coo_product->buildDataArray($this->coo_product->data);
            }
    
            $productListingService                 = StaticGXCoreLoader::getService('ProductListingDisplayService');
            $productListing                        = $productListingService->getSpecials($specialProducts);
            $this->content_array['module_content'] = $this->generateSpecialsListing($productListing);
        }
    }
    
    
    protected function save_data_to_cache($p_cache_key)
    {
        $t_cache_data_array                   = [];
        $t_cache_data_array['module_content'] = $this->content_array['module_content'];
        $t_cache_data_array['pager']          = $this->pager;
        
        if(!empty($this->content_array['module_content'])) {
            $this->coo_cache->set_data($p_cache_key, $t_cache_data_array, true, ['TEMPLATE', 'CHECKOUT']);
        }
    }
    
    
    protected function createPager($page, $query, $perPage)
    {
        $result         = xtc_db_query($query);
        $totalItemCount = (int)xtc_db_num_rows($result);
        
        return ExtendedInformationPager::createExtendedInformationPager(
            $page,
            $perPage,
            $totalItemCount,
            'page'
        );
    }
    
    
    protected function generateSpecialsListing($products)
    {
        $showRating = false;
        if (gm_get_conf('ENABLE_RATING') === 'true' && gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true') {
            $showRating = true;
        }
        
        $showManufacturerImages = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
        $showProductRibbons     = gm_get_conf('SHOW_PRODUCT_RIBBONS');
        
        $swiperData = [
            'products'               => $products,
            'id'                     => 'specials',
            'hoverable'              => true,
            'template'               => 'product_listing.html',
            'truncate'               => gm_get_conf('TRUNCATE_PRODUCTS_NAME'),
            'showRating'             => $showRating,
            'showManufacturerImages' => $showManufacturerImages,
            'showProductRibbons'     => $showProductRibbons,
        ];
        
        $swiperHtml = MainFactory::create_object('ProductsSwiperThemeContentView', [$swiperData]);
        
        return $swiperHtml->get_html();
    }

}
