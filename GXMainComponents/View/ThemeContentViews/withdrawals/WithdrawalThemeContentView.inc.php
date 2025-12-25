<?php
/* --------------------------------------------------------------
   WithdrawalThemeContentView.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

use GXModules\Gambio\AntiSpam\Shop\classes\AntiSpamUtilityTrait;

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMJSON.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderElements.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderSurfaces.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderSurfacesManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderManager.php');


class WithdrawalThemeContentView extends ThemeContentView
{
    use AntiSpamUtilityTrait;
    
    //No flat assigns for gambio template
    public function init_smarty()
    {
        parent::init_smarty();
        $this->set_flat_assigns(false);
    }
    
    
    public function prepare_data()
    {
        $this->add_secret_anti_spam_token();
        
        if (isset($this->content_array['order'])) {
            $t_date_purchased                                     = $this->content_array['order']->info['date_purchased'];
            $t_date_purchased                                     = date(DATE_FORMAT . ' H:i:s',
                                                                         strtotime($t_date_purchased));
            $this->content_array['order']->info['date_purchased'] = $t_date_purchased;
            
            $coo_gm_gprint_content_manager = new GMGPrintContentManager();
            
            for ($i = 0; $i < count($this->content_array['order']->products); $i++) {
                
                $coo_gm_gprint_order_data = $coo_gm_gprint_content_manager->get_orders_products_content($this->content_array['order']->products[$i]['orders_products_id'],
                                                                                                        true);
                
                for ($m = 0; $m < count($coo_gm_gprint_order_data); $m++) {
                    $this->content_array['order']->products[$i]['attributes'][] = [
                        'option' => $coo_gm_gprint_order_data[$m]['NAME'],
                        'value'  => $coo_gm_gprint_order_data[$m]['VALUE']
                    ];
                }
                
                $this->content_array['order']->products[$i]['qty'] = (double)$this->content_array['order']->products[$i]['qty'];
            }
        } else {
            $this->set_content_data('order', null);
        }
    }
    
    
    public function set_download_template()
    {
        $this->set_content_template('content_download.html');
    }
    
    
    public function set_withdrawal_pdf_form_template()
    {
        $this->set_content_template('withdrawal_pdf_form.html');
    }
    
    
    public function set_withdrawal_web_form_template()
    {
        $this->set_content_template('withdrawal_web_form.html');
    }
    
    /**
     * @return void
     *
     * @throws Exception
     */
    protected function add_secret_anti_spam_token(): void
    {
        $this->content_array['secret_token_anti_spam'] = $this->generateSecretAntiSpamToken();
        $this->content_array['fake_hash']              = sha1(random_bytes(256));
    }
}
