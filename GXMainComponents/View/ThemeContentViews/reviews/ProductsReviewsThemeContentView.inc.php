<?php
/* --------------------------------------------------------------
  ProductReviewsContenView.inc.php 2023-04-17
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(product_reviews.php,v 1.47 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (product_reviews.php,v 1.12 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews.php 1238 2005-09-24 10:51:19Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once DIR_FS_INC . 'xtc_row_number_format.inc.php';
require_once DIR_FS_INC . 'xtc_date_short.inc.php';

class ProductsReviewsThemeContentView extends ThemeContentView
{
    protected $products_id;
    protected $products_name;
    protected $language;
    protected $get_params;
    protected $get_params_back;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('product_reviews.html');
        $this->set_flat_assigns(true);
    }
    
    
    protected function set_validation_rules()
    {
        // SET VALIDATION RULES
        $this->validation_rules_array['products_id']     = ['type' => 'int'];
        $this->validation_rules_array['products_name']   = ['type' => 'string'];
        $this->validation_rules_array['language']        = ['type' => 'string'];
        $this->validation_rules_array['get_params']      = ['type' => 'string'];
        $this->validation_rules_array['get_params_back'] = ['type' => 'string'];
    }
    
    
    public function prepare_data()
    {
        $this->content_array['PRODUCTS_NAME'] = $this->products_name;
        
        $this->content_array['PRODUCTS_LINK'] = xtc_href_link(FILENAME_PRODUCT_INFO,
                                                              xtc_product_link($this->products_id,
                                                                               $this->products_name));
        
        $t_query  = 'SELECT
						*
					FROM
						' . TABLE_REVIEWS . '
					WHERE
						products_id = "' . $this->products_id . '"
					ORDER BY
						reviews_id DESC';
        $t_result = xtc_db_query($t_query);
        if (xtc_db_num_rows($t_result)) {
            while ($t_review = xtc_db_fetch_array($t_result)) {
                $this->content_array['module_content'][] = [
                    'ID'               => $t_review['reviews_id'],
                    'AUTHOR_NAME'      => $t_review['customers_name'],
                    'DATE'             => xtc_date_short($t_review['date_added']),
                    'RATING_IMAGE_URL' => StaticGXCoreLoader::getThemeControl()->getThemeImagePath() . 'stars_'
                                          . $t_review['reviews_rating'] . '.gif',
                    'TEXT'             => $t_review['reviews_text']
                ];
            }
        }
    }
}
