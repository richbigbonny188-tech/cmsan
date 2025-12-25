<?php
/* --------------------------------------------------------------
   SSOCreateAccountThemeContentView.inc.php 2022-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SSOCreateAccountThemeContentView extends SSOCreateAccountThemeContentView_parent
{
    protected function add_password()
    {
        parent::add_password();
        
        $this->content_array['form_data']['useSso'] = false;
        $moduleInstalled                            = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        if ($moduleInstalled === true) {
            if ($this->guest_account === false && !empty($this->customer_data_array['password'])) {
                $this->content_array['form_data']['useSso'] = array_key_exists('useSso',
                                                                               $this->customer_data_array) ? $this->customer_data_array['useSso'] : false;
                
                $this->content_array['form_data']['password']['value']     = htmlspecialchars_wrapper($this->customer_data_array['password']);
                $this->content_array['form_data']['confirmation']['value'] = htmlspecialchars_wrapper($this->customer_data_array['password']);
            }
            
            $advancedPayments = MainFactory::create('AmazonAdvancedPayment');
            if (($_SESSION['ssoData']['iss'] ?? null) === 'amazon.com' && $advancedPayments->is_enabled()) {
                setcookie('amazon_Login_accessToken', $_SESSION['ssoData']['access_token'], 0, '/', '', true);
                setcookie('amazon_Login_state_cache', '');
                $ssoConfiguration                  = MainFactory::create('SingleSignonConfigurationStorage');
                $this->content_array['amazon_sso'] = [
                    'access_token'   => $_SESSION['ssoData']['access_token'],
                    'client_id'      => $ssoConfiguration->get('services/amazon/clientId'),
                    'widgets_url'    => $advancedPayments->get_lpa_widgets_url(),
                    'seller_id'      => $advancedPayments->seller_id,
                    'controller_url' => xtc_href_link('shop.php', 'do=AmazonSso/GetAddress', 'SSL'),
                ];
            }
        }
    }
}
