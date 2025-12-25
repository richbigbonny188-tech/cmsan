<?php
/* --------------------------------------------------------------
   GVSendThemeContentView.inc.php 2018-11-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (gv_send.php,v 1.1.2.3 2003/05/12); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_send.php 1034 2005-07-15 15:21:43Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class GVSendThemeContentView extends ThemeContentView
{
    protected $main_message;
    protected $personal_message;
    protected $message_body;
    protected $send_name;
    protected $to_name;
    protected $email;
    protected $amount;
    protected $error_amount;
    protected $error_email;
    protected $action            = '';
    protected $gvDeposit         = 0;
    protected $gvDepositCurrency = '';
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('voucher_send.html');
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['personal_message'] = ['type' => 'string'];
        $this->validation_rules_array['main_message']     = ['type' => 'string'];
        $this->validation_rules_array['message_body']     = ['type' => 'string'];
        $this->validation_rules_array['send_name']        = ['type' => 'string'];
        $this->validation_rules_array['to_name']          = ['type' => 'string'];
        $this->validation_rules_array['email']            = ['type' => 'string'];
        $this->validation_rules_array['amount']           = ['type' => 'string'];
        $this->validation_rules_array['error_amount']     = ['type' => 'string'];
        $this->validation_rules_array['error_email']      = ['type' => 'string'];
        $this->validation_rules_array['action']           = ['type' => 'string'];
    }
    
    
    public function prepare_data()
    {
        switch ($this->action) {
            case 'process':
                $this->process_action();
                
                break;
            case 'send':
                $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                                'main_message',
                                                                                'message_body',
                                                                                'send_name',
                                                                                'to_name',
                                                                                'email',
                                                                                'amount'
                                                                            ]);
                
                if (empty($t_uninitialized_array)) {
                    $this->send_action();
                } else {
                    trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                                  . get_class($this) . " or are null",
                                  E_USER_ERROR);
                }
                
                break;
            default:
                $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                                'error_email',
                                                                                'error_amount',
                                                                                'to_name',
                                                                                'email',
                                                                                'amount',
                                                                                'message_body'
                                                                            ]);
                
                if (empty($t_uninitialized_array)) {
                    $this->default_action();
                } else {
                    trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                                  . get_class($this) . " or are null",
                                  E_USER_ERROR);
                }
                
                break;
        }
    }
    
    
    protected function process_action()
    {
        $this->content_array['action']        = $this->action;
        $this->content_array['CONTINUE_LINK'] = xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL');
    }
    
    
    protected function send_action()
    {
        $this->content_array['action']          = $this->action;
        $t_form_action                          = xtc_href_link(FILENAME_GV_SEND, 'action=process', 'SSL');
        $this->content_array['FORM_ACTION_URL'] = $t_form_action;
        $this->content_array['MAIN_MESSAGE']    = $this->main_message;
        
        if ($this->message_body) {
            $this->content_array['POST_MESSAGE'] = htmlentities_wrapper($this->message_body);
        }
        
        $this->content_array['HIDDEN_FIELDS']   = [];
        $this->content_array['HIDDEN_FIELDS'][] = ['name' => 'send_name', 'value' => $this->send_name];
        $this->content_array['HIDDEN_FIELDS'][] = [
            'name'  => 'to_name',
            'value' => htmlentities_wrapper($this->to_name)
        ];
        $this->content_array['HIDDEN_FIELDS'][] = ['name' => 'email', 'value' => htmlentities_wrapper($this->email)];
        $this->content_array['HIDDEN_FIELDS'][] = ['name' => 'amount', 'value' => $this->amount];
        $this->content_array['HIDDEN_FIELDS'][] = [
            'name'  => 'message_body',
            'value' => htmlentities_wrapper($this->message_body)
        ];
        
        $this->content_array['LINK_BACK_URL'] = xtc_href_link(FILENAME_GV_SEND);
    }
    
    
    protected function default_action()
    {
        $this->content_array['action']            = '';
        $this->content_array['gvDepositCurrency'] = $this->gvDepositCurrency;
        $t_form_action                            = xtc_href_link(FILENAME_GV_SEND, 'action=send', 'SSL');
        $this->content_array['FORM_ACTION_URL']   = $t_form_action;
        $this->content_array['INPUT_TO_NAME']     = isset($GLOBALS['to_name']) ? $GLOBALS['to_name'] : htmlentities_wrapper(gm_prepare_string($this->to_name,
                                                                                                                                              true));
        $this->content_array['INPUT_EMAIL']       = isset($GLOBALS['email']) ? $GLOBALS['email'] : htmlentities_wrapper(gm_prepare_string($this->email,
                                                                                                                                          true));
        $this->content_array['ERROR_EMAIL']       = $this->error_email;
        $this->content_array['INPUT_AMOUNT']      = htmlentities_wrapper($this->amount);
        $this->content_array['INPUT_AMOUNT_MAX']  = $this->gvDeposit;
        $this->content_array['ERROR_AMOUNT']      = $this->error_amount;
        $this->content_array['TEXTAREA_MESSAGE']  = isset($GLOBALS['message_body']) ? $GLOBALS['message_body'] : htmlentities_wrapper($this->message_body);
    }
}
