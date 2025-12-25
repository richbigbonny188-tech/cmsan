<?php
/* --------------------------------------------------------------
  SpecialsBoxThemeContentView.inc.php 2023-09-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(specials.php,v 1.30 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (specials.php,v 1.10 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: specials.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_random_select.inc.php');

class SpecialsBoxThemeContentView extends ThemeContentView
{
    protected $coo_product;
    protected $sql_result;
    protected $result_hard_limit = 30;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('box_specials.html');
        $this->set_caching_enabled(false);
    }
    
    
    public function prepare_data()
    {
        $this->build_html = false;
        
        $t_uninitialized_array = $this->get_uninitialized_variables(['coo_product']);
        if (empty($t_uninitialized_array)) {
            //fsk18 lock
            $t_fsk_lock = '';
            if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
                $t_fsk_lock = ' AND p.products_fsk18 != 1';
            }
            
            $t_group_check = '';
            if (GROUP_CHECK == 'true') {
                $t_group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id']
                                 . " = 1 ";
            }
            
            $query  = "SELECT
												p.products_id,
												pd.products_name,
												pd.gm_alt_text,
												pd.products_meta_description,
												p.products_price,
												p.products_tax_class_id,
												p.products_image,
												p.products_image_w,
												p.products_image_h,
												s.expires_date,
												p.products_vpe,
												p.products_vpe_status,
												p.products_vpe_value,
												s.specials_new_products_price
											FROM
												" . TABLE_PRODUCTS . " p,
												" . TABLE_PRODUCTS_DESCRIPTION . " pd,
												" . TABLE_SPECIALS . " s
											WHERE
												p.products_status = '1' AND
												p.products_id = s.products_id AND
												pd.products_id = s.products_id AND
												pd.language_id = '" . $_SESSION['languages_id'] . "' AND
												s.status = '1'
												" . $t_group_check . "
												" . $t_fsk_lock . "
											ORDER BY s.specials_date_added DESC
											LIMIT " . min((int)MAX_RANDOM_SELECT_SPECIALS, $this->result_hard_limit);
            $result = xtc_db_query($query);
            
            if (xtc_db_num_rows($result) > 0 || StyleEditServiceFactory::service()->isEditing()) {
                $specials = [];
                
                while ($special = xtc_db_fetch_array($result)) {
                    $product    = MainFactory::create_object('product', [$special['products_id']]);
                    $specials[] = $product->buildDataArray($product->data);
                }
                
                $productListingService = StaticGXCoreLoader::getService('ProductListingDisplayService');
                $productListing        = $productListingService->getSpecialsBox($specials);
                $this->build_html      = true;
                
                if ($productListing) {
                    $randomArrayKey                       = array_rand($productListing);
                    $this->content_array['box_content']   = $productListing[$randomArrayKey];
                    $this->content_array['SPECIALS_LINK'] = xtc_href_link(FILENAME_SPECIALS);
                }
            }
        } else {
            trigger_error("Variable(s) " . implode(', ',
                                                   $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    protected function set_validation_rules()
    {
        // SET VALIDATION RULES
        $this->validation_rules_array['coo_product'] = [
            'type'        => 'object',
            'object_type' => 'product',
        ];
        $this->validation_rules_array['sql_result']  = ['type' => 'array'];
    }
}
