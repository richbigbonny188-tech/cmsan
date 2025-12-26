<?php
/* --------------------------------------------------------------
  SplitNavigationThemeContentView.php 2019-08-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class SplitNavigationThemeContentView
 */
class SplitNavigationThemeContentView extends ThemeContentView
{
    /**
     * @var array
     */
    protected $error_array = [];
    
    /**
     * @var array
     */
    protected $customer_data_array = [];
    
    /**
     * is set in system/classes/listing/ProductListingContentControl.inc.php:249
     * @var splitPageResults_ORIGIN
     */
    protected $coo_split_page_results;
    
    
    /**
     * SplitNavigationThemeContentView constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('gm_navigation.html');
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables(['coo_split_page_results']);
        
        if (empty($t_uninitialized_array)) {
            $this->content_array['LEFT']  = $this->coo_split_page_results->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW);
            $this->content_array['RIGHT'] = TEXT_RESULT_PAGE . ' '
                                            . $this->coo_split_page_results->display_links(MAX_DISPLAY_PAGE_LINKS,
                                                                                           xtc_get_all_get_params([
                                                                                                                      'page',
                                                                                                                      'info',
                                                                                                                      'x',
                                                                                                                      'y',
                                                                                                                      'language',
                                                                                                                      'currency',
                                                                                                                      'gm_boosted_category',
                                                                                                                      'gm_boosted_content',
                                                                                                                      'gm_boosted_product'
                                                                                                                  ]));
            $this->coo_split_page_results->setPrevNextUrls();
        } else {
            trigger_error('Variable(s) ' . implode(', ', $t_uninitialized_array) . ' do(es) not exist in class '
                          . get_class($this) . ' or is/are null',
                          E_USER_ERROR);
        }
    }
    
    
    /**
     *
     */
    protected function set_validation_rules(): void
    {
        // SET VALIDATION RULES
        $this->validation_rules_array['coo_split_page_results'] = [
            'type'        => 'object',
            'object_type' => 'splitPageResults'
        ];
    }
}