<?php
/* --------------------------------------------------------------
   IndexThemeContentView.inc.php 2024-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
  (c) 2003  nextcommerce (default.php,v 1.11 2003/08/22); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: default.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        Autor: Mikel Williams | mikel@ladykatcostumes.com
  Customers Status v3.x  © 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs...by=date#dirlist

  Released under the GNU General Public License
  ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_customer_greeting.inc.php');

class IndexThemeContentView extends ThemeContentView
{
    protected $languages_id;
    protected $customers_status_id;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('home.html');
        $this->set_flat_assigns(true);
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['languages_id']        = ['type' => 'int'];
        $this->validation_rules_array['customers_status_id'] = ['type' => 'int'];
    }
    
    
    public function prepare_data()
    {
        if(in_array('Honeygrid',StaticGXCoreLoader::getThemeControl()->getCurrentThemeHierarchy()) || strpos(StaticGXCoreLoader::getThemeControl()->getCurrentTheme(),'Honeygrid') !== false) {
            $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                            'languages_id',
                                                                            'customers_status_id'
                                                                        ]);
            
            if (empty($t_uninitialized_array)) {
                $this->load_content_data(5);
                $this->load_content_data(10, 'center');
                $this->load_content_data(11, 'bottom');
                
                $this->load_center_modules();
            } else {
                trigger_error("Variable(s) " . implode(', ',
                                                       $t_uninitialized_array) . " do(es) not exist in class "
                              . get_class($this) . " or is/are null",
                              E_USER_ERROR);
            }
        }
    }
    
    
    protected function get_select_statement($p_content_group_id)
    {
        $group_check = '';
        if (GROUP_CHECK == 'true') {
            $group_check = 'AND group_ids LIKE "%c_' . $this->customers_status_id . '_group%"';
        }
        
        $t_select = 'SELECT
							content_title,
							content_heading,
							content_text,
							content_file
						FROM
							' . TABLE_CONTENT_MANAGER . '
						WHERE
							content_group = "' . (int)$p_content_group_id . '"
						AND content_status = 1
							' . ($group_check ?? null) . '
							AND languages_id = "' . $this->languages_id . '"
		';
        
        return $t_select;
    }
    
    
    protected function load_content_data($p_content_group_id, $p_key = '')
    {
        $t_select = $this->get_select_statement($p_content_group_id);
        $t_result = xtc_db_query($t_select);
        if (xtc_db_num_rows($t_result)) {
            $t_shop_content_array = xtc_db_fetch_array($t_result);
            
            $t_key = 'title';
            if (empty($p_key) == false) {
                $t_key .= '_' . $p_key;
            }
            $this->content_array[$t_key] = $t_shop_content_array['content_heading'];
            
            if ($t_shop_content_array['content_file'] != '') {
                ob_start();
                if (strpos($t_shop_content_array['content_file'], '.txt')) {
                    echo '<pre>';
                }
                
                include(DIR_FS_CATALOG . 'media/content/' . $t_shop_content_array['content_file']);
                
                if (strpos($t_shop_content_array['content_file'], '.txt')) {
                    echo '</pre>';
                }
                $t_shop_content_array['content_text'] = ob_get_contents();
                ob_end_clean();
            }
            
            $t_key = 'text';
            if (empty($p_key) == false) {
                $t_key .= '_' . $p_key;
            }
            $this->content_array[$t_key] = str_replace('{$greeting}',
                                                       xtc_customer_greeting(),
                                                       $t_shop_content_array['content_text']);
        }
    }
    
    
    protected function load_center_modules()
    {
        //TOP
        $coo_top_products_content_view = MainFactory::create_object('TopProductsMainThemeContentView');
        $coo_top_products_content_view->set_('customers_fsk18_display',
                                             $_SESSION['customers_status']['customers_fsk18_display']);
        $coo_top_products_content_view->set_('customers_status_id',
                                             $_SESSION['customers_status']['customers_status_id']);
        $coo_top_products_content_view->set_('languages_id', $_SESSION['languages_id']);
        $this->content_array['MODULE_new_products'] = $coo_top_products_content_view->get_html();
        
        //UPCOMING
        $coo_upcoming_products_content_view = MainFactory::create_object('UpcomingProductsMainThemeContentView');
        $coo_upcoming_products_content_view->set_('customers_fsk18_display',
                                                  $_SESSION['customers_status']['customers_fsk18_display']);
        $coo_upcoming_products_content_view->set_('customers_status_id',
                                                  $_SESSION['customers_status']['customers_status_id']);
        $coo_upcoming_products_content_view->set_('languages_id', $_SESSION['languages_id']);
        $coo_upcoming_products_content_view->set_('upcoming_products_count', MAX_DISPLAY_UPCOMING_PRODUCTS);
        $this->content_array['MODULE_upcoming_products'] = $coo_upcoming_products_content_view->get_html();
        
        //SPECIALS
        $coo_specials_main_content_view = MainFactory::create_object('SpecialsMainThemeContentView');
        $coo_specials_main_content_view->set_('customers_fsk18_display',
                                              $_SESSION['customers_status']['customers_fsk18_display']);
        $coo_specials_main_content_view->set_('customers_status_id',
                                              $_SESSION['customers_status']['customers_status_id']);
        $coo_specials_main_content_view->set_('languages_id', $_SESSION['languages_id']);
        $coo_specials_main_content_view->set_('specials_count', gm_get_conf("GM_SPECIALS_STARTPAGE"));
        $this->content_array['specials_main'] = $coo_specials_main_content_view->get_html();
        
        //NEW
        $coo_new_products_main_content_view = MainFactory::create_object('NewProductsMainThemeContentView');
        $coo_new_products_main_content_view->set_('customers_status_id',
                                                  $_SESSION['customers_status']['customers_status_id']);
        $coo_new_products_main_content_view->set_('languages_id', $_SESSION['languages_id']);
        $coo_new_products_main_content_view->set_('customers_fsk18_display',
                                                  $_SESSION['customers_status']['customers_fsk18_display']);
        $coo_new_products_main_content_view->set_('new_products_count', gm_get_conf('GM_NEW_PRODUCTS_STARTPAGE'));
        $this->content_array['products_new_main'] = $coo_new_products_main_content_view->get_html();
    }
}
