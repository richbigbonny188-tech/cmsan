<?php
/* --------------------------------------------------------------
	AmazonMailThemeContentView.inc.php 2023-10-26
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2023 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonMailThemeContentView extends ThemeContentView
{
    protected $language;
    protected $language_id;
    protected $mail_logo;
    protected $name;
    protected $orders_id;
    protected $orderdate;
    
    
    public function __construct()
    {
        parent::__construct();
        // NO CACHING
        $this->set_caching_enabled(false);
        // ACTIVATE FLAT MODE (direct assign)
        $this->set_flat_assigns(true);
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['language_id'] = ['type' => 'int'];
        $this->validation_rules_array['language']    = ['type' => 'string'];
        $this->validation_rules_array['mail_logo']   = ['type' => 'string'];
        $this->validation_rules_array['name']        = ['type' => 'string'];
        $this->validation_rules_array['orders_id']   = ['type' => 'string'];
        $this->validation_rules_array['orderdate']   = ['type' => 'string'];
    }
    
    
    public function get_mail_content_array($p_hardfail = false)
    {
        $t_html_output_array = [];
        
        // INIT
        $t_language    = $this->language;
        $t_language_id = $this->language_id;
        
        if (defined('EMAIL_SIGNATURE')) {
            $this->set_content_data('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
        }
        if (defined('EMAIL_HTML_SIGNATURE')) {
            $this->set_content_data('EMAIL_SIGNATURE_HTML', EMAIL_HTML_SIGNATURE);
        }
        $this->set_content_data('gm_logo_mail', $this->mail_logo);
        
        // SET CONTENT DATA
        $this->set_content_data('NAME', $this->name);
        $this->set_content_data('ORDERS_ID', $this->orders_id);
        $this->set_content_data('ORDERDATE', $this->orderdate);
        
        // GET HTML MAIL CONTENT
        if ($p_hardfail == true) {
            $t_html_output_array['html'] = fetch_email_template($this,
                                                                'amazonadvpay_mail_hard',
                                                                'html',
                                                                '',
                                                                $t_language_id,
                                                                $t_language);
        } else {
            $t_html_output_array['html'] = fetch_email_template($this,
                                                                'amazonadvpay_mail',
                                                                'html',
                                                                '',
                                                                $t_language_id,
                                                                $t_language);
        }
        
        // SET CONTENT DATA
        $this->set_content_data('body_text', strip_tags($this->body_text));
        
        // GET TXT MAIL CONTENT
        if ($p_hardfail == true) {
            $t_html_output_array['txt'] = fetch_email_template($this,
                                                               'amazonadvpay_mail_hard',
                                                               'txt',
                                                               '',
                                                               $t_language_id,
                                                               $t_language);
        } else {
            $t_html_output_array['txt'] = fetch_email_template($this,
                                                               'amazonadvpay_mail',
                                                               'txt',
                                                               '',
                                                               $t_language_id,
                                                               $t_language);
        }
        
        // RETURN MAIL CONTENTS
        return $t_html_output_array;
    }
    
    
    public function fetch($p_filepath)
    {
        // WORKAROUND, da fetch_mail_template fetch-Methode aufruft (nicht existent in ThemeContentView)
        $this->set_template_dir(DIR_FS_CATALOG);
        $this->set_content_template(str_replace(DIR_FS_CATALOG, '', $p_filepath));
        
        return $this->build_html();
    }
    
}
