<?php
/* --------------------------------------------------------------
   ProductListingContentView.inc.php 2019-09-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_listing.php,v 1.42 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (product_listing.php,v 1.19 2003/08/1); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_listing.php 1286 2005-10-07 10:10:18Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class ProductListingThemeContentView extends ThemeContentView
{
    protected $cache_id_parameter_array;
    protected $category_description;
    protected $category_description_bottom;
    protected $category_heading_title;
    protected $category_image;
    protected $category_image_alt_text;
    protected $category_name;
    protected $filter_selection_html;
    protected $get_params_hidden_data_array;
    protected $listing_count;
    protected $listing_sort;
    protected $manufacturers_data_array;
    protected $manufacturers_id;
    protected $manufacturers_dropdown;
    protected $navigation_html;
    protected $navigation_info_html;
    protected $navigation_pages_count;
    protected $page_number;
    protected $products_array;
    protected $products_per_page;
    protected $search_keywords;
    protected $show_quantity;
    protected $sorting_form_action_url;
    protected $thumbnail_width;
    protected $view_mode;
    protected $view_mode_url_default;
    protected $view_mode_url_tiled;
    protected $showRating;
    protected $showManufacturerImages;
    protected $showProductRibbons;
    protected $showCategoriesImageInDescription;
    protected $pager;
    protected $languageTextManager;
    protected $maxDisplayPageLinks;
    protected $category_id = 0;
    protected $c_path;
    /**
     * @var MenuBoxDataContainerInterface
     */
    protected $coo_mn_data_container;
    
    
    public function __construct($p_template = 'default')
    {
        parent::__construct();
        
        $this->set_template($p_template);
        
        $this->set_flat_assigns(true);
    }
    
    
    public function set_template($template)
    {
        $this->set_content_template($this->get_template_name($template));
    }
    
    
    /**
     * @param $template
     *
     * @return string
     */
    public function get_template_name($template)
    {
        return $this->get_default_template(DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()
                                               ->getProductListingTemplatePath(),
                                           'product_listing_template_',
                                           $template);
    }
    
    
    public function prepare_data()
    {
        parent::prepare_data();
        $this->prepare_menu_boxes();
        
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'cache_id_parameter_array',
                                                                        'get_params_hidden_data_array',
                                                                        'navigation_html',
                                                                        'navigation_info_html',
                                                                        'navigation_pages_count',
                                                                        'products_array',
                                                                        'products_per_page',
                                                                        'show_quantity',
                                                                        'sorting_form_action_url',
                                                                        'view_mode',
                                                                        'view_mode_url_default',
                                                                        'view_mode_url_tiled',
                                                                    ]);
        
        if (empty($t_uninitialized_array)) {
            $this->set_content_data('CATEGORIES_ID', $this->category_id);
            $this->set_content_data('CATEGORIES_DESCRIPTION', $this->category_description);
            $this->set_content_data('CATEGORIES_DESCRIPTION_BOTTOM', $this->category_description_bottom);
            $this->set_content_data('CATEGORIES_GM_ALT_TEXT', htmlspecialchars_wrapper($this->category_image_alt_text));
            $this->set_content_data('CATEGORIES_HEADING_TITLE',
                                    htmlspecialchars_wrapper($this->category_heading_title));
            $this->set_content_data('SHOW_CATEGORIES_IMAGE_IN_DESCRIPTION', $this->showCategoriesImageInDescription);
            $this->set_content_data('CATEGORIES_IMAGE', $this->category_image);
            $this->set_content_data('CATEGORIES_NAME', htmlspecialchars_wrapper($this->category_name));
            
            $t_start_count_value = $this->products_per_page;
            $t_count_value_2     = $t_start_count_value * 2;
            $t_count_value_3     = $t_start_count_value + $t_count_value_2;
            $t_count_value_4     = $t_count_value_3 * 2;
            $t_count_value_5     = $t_count_value_4 * 2;
            $this->set_content_data('COUNT_VALUE_1', $t_start_count_value);
            $this->set_content_data('COUNT_VALUE_2', $t_count_value_2);
            $this->set_content_data('COUNT_VALUE_3', $t_count_value_3);
            $this->set_content_data('COUNT_VALUE_4', $t_count_value_4);
            $this->set_content_data('COUNT_VALUE_5', $t_count_value_5);
            
            $this->set_content_data('FILTER_SELECTION', $this->filter_selection_html);
            $this->set_content_data('get_params_hidden_data', $this->get_params_hidden_data_array);
            
            if ($this->show_quantity === true) {
                $this->set_content_data('GM_SHOW_QTY', '1');
            } else {
                $this->set_content_data('GM_SHOW_QTY', '0');
            }
            
            $this->set_content_data('gm_manufacturers_id', $this->manufacturers_id);
            $this->set_content_data('HIDDEN_QTY_NAME', 'products_qty');
            $this->set_content_data('HIDDEN_QTY_VALUE', '1');
            
            if ($this->listing_count !== null) {
                $this->set_content_data('ITEM_COUNT', htmlspecialchars_wrapper($this->listing_count));
            }
            
            $this->set_content_data('manufacturers_data', $this->manufacturers_data_array);
            $this->set_content_data('MANUFACTURER_DROPDOWN', $this->manufacturers_dropdown);
            $this->set_content_data('module_content', $this->products_array);
            
            $this->set_content_data('pager', $this->pager);
            
            $this->set_content_data('pages',
                                    $this->getPages($this->pager->page(),
                                                    $this->maxDisplayPageLinks,
                                                    $this->pager->totalPageCount()));
            
            $this->set_content_data('NAVIGATION', $this->navigation_html);
            $this->set_content_data('bar', $this->navigation_html);
            $this->set_content_data('NAVIGATION_INFO', $this->navigation_info_html);
            $this->set_content_data('NAVIGATION_PAGES_COUNT', $this->navigation_pages_count);
            $this->set_content_data('info', $this->navigation_info_html);
            
            $navigationUrl = splitPageResults::get_navigation_url();
            $this->set_content_data('navigation_url', $navigationUrl);
            $this->set_content_data('page_param', strpos($navigationUrl, '?') !== false ? '&page=' : '?page=');
            
            if (isset($this->search_keywords)) {
                $this->set_content_data('SEARCH_RESULT_PAGE', 1);
                $this->set_content_data('KEYWORDS', gm_prepare_string($this->search_keywords, true));
            }
            
            if ($this->listing_sort !== null) {
                $this->set_content_data('SORT', htmlspecialchars_wrapper($this->listing_sort));
            }
            
            $this->set_content_data('SORTING_FORM_ACTION_URL',
                                    htmlspecialchars_wrapper($this->sorting_form_action_url));
            $this->set_content_data('VIEW_MODE', $this->view_mode);
            $this->set_content_data('VIEW_MODE_URL_DEFAULT', $this->view_mode_url_default);
            $this->set_content_data('VIEW_MODE_URL_TILED', $this->view_mode_url_tiled);
            $this->set_content_data('showManufacturerImages', $this->showManufacturerImages);
            $this->set_content_data('showProductRibbons', $this->showProductRibbons);
            $this->set_content_data('showRating', $this->showRating);
            $this->set_content_data('SHOW_PRODUCTS_MODEL',
                                    gm_get_conf('SHOW_PRODUCTS_MODEL_IN_PRODUCT_LISTS') === 'true');
            
            $this->add_cache_id_elements($this->cache_id_parameter_array);
        } else {
            trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    /**
     *
     */
    protected function prepare_menu_boxes(): void
    {
        if ($this->coo_mn_data_container) {
            foreach ($this->coo_mn_data_container->MenuBoxData() as $key => $value) {
                $this->set_content_data($key, $value);
            }
        }
    }
    
    
    protected function getPages($currentPage, $maxPageNumber, $maxPage)
    {
        $pages = [];
        
        $checkSum   = 1 + (($maxPageNumber - 1) * 2) + 2;
        $countIndex = 0;
        
        if ($checkSum >= $maxPage) {
            for ($i = 1; $i <= $maxPage; $i++) {
                $pages[$i - 1]['page']  = $i;
                $pages[$i - 1]['text']  = $i;
                $pages[$i - 1]['title'] = sprintf($this->languageTextManager->get_text('PREVNEXT_TITLE_PAGE_NO',
                                                                                       'general'),
                                                  $pages[$countIndex]['page']);
            }
        } else {
            
            $pagesBeforeAndAfter = $maxPageNumber - 1;
            if ($currentPage - $pagesBeforeAndAfter > 1) {
                $pages[$countIndex]['page']  = 1;
                $pages[$countIndex]['text']  = 1;
                $pages[$countIndex]['title'] = sprintf($this->languageTextManager->get_text('PREVNEXT_TITLE_PAGE_NO',
                                                                                            'general'),
                                                       $pages[$countIndex]['page']);
            }
            if ($currentPage - $pagesBeforeAndAfter > 2) {
                $countIndex                  = count($pages);
                $pages[$countIndex]['page']  = $currentPage - $pagesBeforeAndAfter - 1;
                $pages[$countIndex]['text']  = '...';
                $pages[$countIndex]['title'] = sprintf($this->languageTextManager->get_text('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE',
                                                                                            'general'),
                                                       $maxPageNumber);
            }
            for ($i = 0; $i < $pagesBeforeAndAfter * 2 + 1; $i++) {
                if ($currentPage - $pagesBeforeAndAfter + $i < $maxPage
                    && $currentPage - $pagesBeforeAndAfter + $i > 0) {
                    $countIndex                  = count($pages);
                    $pages[$countIndex]['page']  = $currentPage - $pagesBeforeAndAfter + $i;
                    $pages[$countIndex]['text']  = $currentPage - $pagesBeforeAndAfter + $i;
                    $pages[$countIndex]['title'] = sprintf($this->languageTextManager->get_text('PREVNEXT_TITLE_PAGE_NO',
                                                                                                'general'),
                                                           $pages[$countIndex]['page']);
                }
            }
            if ($pages[count($pages) - 1]['page'] === $maxPage - 2) {
                $countIndex                  = count($pages);
                $pages[$countIndex]['page']  = $maxPage - 1;
                $pages[$countIndex]['text']  = $maxPage - 1;
                $pages[$countIndex]['title'] = sprintf($this->languageTextManager->get_text('PREVNEXT_TITLE_PAGE_NO',
                                                                                            'general'),
                                                       $pages[$countIndex]['page']);
            } elseif ($pages[count($pages) - 1]['page'] < $maxPage - 1) {
                $countIndex                  = count($pages);
                $pages[$countIndex]['page']  = $currentPage + $pagesBeforeAndAfter + 1;
                $pages[$countIndex]['text']  = '...';
                $pages[$countIndex]['title'] = sprintf($this->languageTextManager->get_text('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE',
                                                                                            'general'),
                                                       $maxPageNumber);
            }
            $countIndex                  = count($pages);
            $pages[$countIndex]['page']  = $maxPage;
            $pages[$countIndex]['text']  = $maxPage;
            $pages[$countIndex]['title'] = sprintf($this->languageTextManager->get_text('PREVNEXT_TITLE_PAGE_NO',
                                                                                        'general'),
                                                   $pages[$countIndex]['page']);
        }
        
        return $pages;
    }
    
    
    public function setProductListingMainTemplate()
    {
        $this->set_content_template('product_listing_main.html');
    }
    
    
    public function setPaginationInfoTemplate()
    {
        $this->set_content_template('pagination_info.html');
    }
    
    
    public function setProductListingHiddenFieldsTemplate()
    {
        $this->set_content_template('product_listing_hidden_fields.html');
    }
    
    
    /**
     * @deprecated
     */
    public function setProductInfoProductListsTemplate()
    {
        $this->set_content_template('product_info_product_lists.html');
    }
    
    
    public function setProductListingPaginationTemplate()
    {
        $this->set_content_template('pagination.html');
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['cache_id_parameter_array']     = ['type' => 'array'];
        $this->validation_rules_array['category_description']         = ['type' => 'string'];
        $this->validation_rules_array['category_description_bottom']  = ['type' => 'string'];
        $this->validation_rules_array['category_heading_title']       = ['type' => 'string'];
        $this->validation_rules_array['category_id']                  = ['type' => 'int'];
        $this->validation_rules_array['category_image']               = ['type' => 'string'];
        $this->validation_rules_array['category_image_alt_text']      = ['type' => 'string'];
        $this->validation_rules_array['category_name']                = ['type' => 'string'];
        $this->validation_rules_array['filter_selection_html']        = ['type' => 'string'];
        $this->validation_rules_array['get_params_hidden_data_array'] = ['type' => 'array'];
        $this->validation_rules_array['listing_count']                = ['type' => 'int'];
        $this->validation_rules_array['listing_sort']                 = ['type' => 'string'];
        $this->validation_rules_array['manufacturers_data_array']     = ['type' => 'array'];
        $this->validation_rules_array['manufacturers_id']             = ['type' => 'int'];
        $this->validation_rules_array['manufacturers_dropdown']       = ['type' => 'string'];
        $this->validation_rules_array['navigation_html']              = ['type' => 'string'];
        $this->validation_rules_array['navigation_info_html']         = ['type' => 'string'];
        $this->validation_rules_array['navigation_pages_count']       = ['type' => 'int'];
        $this->validation_rules_array['page_number']                  = ['type' => 'int'];
        $this->validation_rules_array['products_array']               = ['type' => 'array'];
        $this->validation_rules_array['products_per_page']            = ['type' => 'int'];
        $this->validation_rules_array['search_keywords']              = ['type' => 'string'];
        $this->validation_rules_array['show_quantity']                = ['type' => 'bool'];
        $this->validation_rules_array['sorting_form_action_url']      = ['type' => 'string'];
        $this->validation_rules_array['thumbnail_width']              = ['type' => 'int'];
        $this->validation_rules_array['view_mode']                    = ['type' => 'string'];
        $this->validation_rules_array['view_mode_url_default']        = ['type' => 'string'];
        $this->validation_rules_array['view_mode_url_tiled']          = ['type' => 'string'];
        $this->validation_rules_array['showRating']                   = ['type' => 'bool'];
        $this->validation_rules_array['showManufacturerImages']       = ['type' => 'string'];
        $this->validation_rules_array['showProductRibbons']           = ['type' => 'string'];
        $this->validation_rules_array['maxDisplayPageLinks']          = ['type' => 'int'];
        $this->validation_rules_array['coo_mn_data_container']        = ['type' => 'object'];
        $this->validation_rules_array['languageTextManager']          = [
            'type'        => 'object',
            'object_type' => 'LanguageTextManager'
        ];
    }
}
