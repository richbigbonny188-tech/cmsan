<?php
/* --------------------------------------------------------------
  LayoutContentControl.inc.php 2021-07-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class LayoutContentControl extends DataProcessing
{
    protected $c_path;
    protected $category_id;
    protected $coo_breadcrumb;
    protected $coo_product;
    protected $coo_payment;
    protected $coo_xtc_price;
    protected $coo_mn_data_container;
    protected $main_content;
    protected $request_type;
    
    
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function proceed()
    {
        $coo_header_control = MainFactory::create_object('HeaderContentControl');
        $coo_header_control->set_data('GET', $this->v_data_array['GET']);
        $coo_header_control->set_data('POST', $this->v_data_array['POST']);
        $coo_header_control->set_('c_path', $this->c_path);
        $coo_header_control->set_('coo_product', $this->coo_product);
        $coo_header_control->set_('xtcPrice', $this->coo_xtc_price);
        
        if ($this->coo_payment !== null) {
            $coo_header_control->set_('coo_payment', $this->coo_payment);
        }
        
        $coo_header_control->proceed();
        
        $t_redirect_url = $coo_header_control->get_redirect_url();
        if (empty($t_redirect_url) == false) {
            $this->set_redirect_url($t_redirect_url);
            
            return true;
        } else {
            $t_head_content = $coo_header_control->get_response();
        }
        
        $t_error_message = '';
        if (isset($_SESSION['gm_error_message']) && xtc_not_null($_SESSION['gm_error_message'])) {
            $t_error_message = urldecode($_SESSION['gm_error_message']);
            unset($_SESSION['gm_error_message']);
        }
        
        $t_info_message = '';
        if (isset($_SESSION['gm_info_message']) && xtc_not_null($_SESSION['gm_info_message'])) {
            $t_info_message = urldecode($_SESSION['gm_info_message']);
            unset($_SESSION['gm_info_message']);
        }
        
        $coo_bottom_control = MainFactory::create_object('BottomContentControl');
        $coo_bottom_control->set_data('GET', $this->v_data_array['GET']);
        $coo_bottom_control->set_data('POST', $this->v_data_array['POST']);
        $coo_bottom_control->set_('c_path', $this->c_path);
        $coo_bottom_control->set_('coo_product', $this->coo_product);
        
        $coo_bottom_control->proceed();
        
        $t_redirect_url = $coo_bottom_control->get_redirect_url();
        if (empty($t_redirect_url) == false) {
            $this->set_redirect_url($t_redirect_url);
            
            return true;
        } else {
            $t_bottom_content = $coo_bottom_control->get_response();
        }
        
        $coo_layout_view = MainFactory::create_object('LayoutThemeContentView');
        $coo_layout_view->set_('bottom_content', $t_bottom_content);
        $coo_layout_view->set_('c_path', $this->c_path);
        
        if ($this->category_id !== null) {
            $coo_layout_view->set_('category_id', $this->category_id);
        }
        $coo_layout_view->set_('coo_breadcrumb', $this->coo_breadcrumb);
        $coo_layout_view->set_('coo_product', $this->coo_product);
        $coo_layout_view->set_('coo_xtc_price', $this->coo_xtc_price);
        $coo_layout_view->set_('error_message', $t_error_message);
        $coo_layout_view->set_('head_content', $t_head_content);
        $coo_layout_view->set_('info_message', $t_info_message);
        $coo_layout_view->set_('main_content', $this->main_content);
        $coo_layout_view->set_('request_type', $this->request_type);
        $coo_layout_view->set_('coo_mn_data_container', $this->get_mn_data_container());
        
        if (isset($_SESSION['customer_id'])) {
            $coo_layout_view->set_('customer_id', $_SESSION['customer_id']);
        }
        
        if ($_SESSION['account_type'] == '0') {
            $coo_layout_view->set_('account_type', $_SESSION['account_type']);
        }
        
        $this->_addTopbarContent($coo_layout_view);
        $this->_addPopupNotificationContent($coo_layout_view);
        $this->_addCookieBarContent($coo_layout_view);
        
        $this->v_output_buffer = $coo_layout_view->get_html();
    }
    
    
    /**
     * @param \ContentViewInterface $layoutView
     */
    protected function _addTopbarContent(ContentViewInterface $layoutView)
    {
        $topbarContent = '';
        
        if (gm_get_conf('TOPBAR_NOTIFICATION_MODE', 'ASSOC', true) === 'permanent'
            || (isset($_SESSION['hide_topbar']) && $_SESSION['hide_topbar'] !== true)
            || !isset($_SESSION['hide_topbar'])) {
            /* @var TopbarContentView $view */
            $view          = MainFactory::create_object('TopbarThemeContentView');
            $topbarContent = $view->get_html();
        }
        
        $layoutView->set_('topbar_content', $topbarContent);
    }
    
    
    /**
     * @param \ContentViewInterface $layoutView
     */
    protected function _addPopupNotificationContent(ContentViewInterface $layoutView)
    {
        $t_popup_content = '';
        
        if (isset($_SESSION['hide_popup_notification']) && $_SESSION['hide_popup_notification'] !== true
            || !isset($_SESSION['hide_popup_notification'])) {
            $view            = MainFactory::create_object('PopupNotificationThemeContentView');
            $t_popup_content = $view->get_html();
        }
        
        $layoutView->set_('popup_notification_content', $t_popup_content);
    }
    
    
    /**
     * @param ContentView $layoutView
     */
    protected function _addCookieBarContent(ContentViewInterface $layoutView)
    {
        /* @var CookieBarContentView $view */
        $view             = MainFactory::create_object('CookieBarThemeContentView');
        $cookieBarContent = $view->get_html();
        $layoutView->set_('cookiebar_content', $cookieBarContent);
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['category_id']           = ['type' => 'int'];
        $this->validation_rules_array['c_path']                = ['type' => 'string', 'strict' => true];
        $this->validation_rules_array['coo_breadcrumb']        = ['type' => 'object', 'object_type' => 'breadcrumb'];
        $this->validation_rules_array['coo_payment']           = ['type' => 'object', 'object_type' => 'payment'];
        $this->validation_rules_array['coo_product']           = ['type' => 'object', 'object_type' => 'product'];
        $this->validation_rules_array['coo_xtc_price']         = ['type' => 'object', 'object_type' => 'xtcPrice'];
        $this->validation_rules_array['main_content']          = ['type' => 'string', 'strict' => true];
        $this->validation_rules_array['request_type']          = ['type' => 'string', 'strict' => true];
        $this->validation_rules_array['coo_mn_data_container'] = ['type' => 'object'];
    }
    
    
    protected function get_mn_data_container()
    {
        if ($this->coo_mn_data_container === null){
            $applicationBottomExtenderComponent = MainFactory::create_object('ApplicationBottomExtenderComponent');
            $applicationBottomExtenderComponent->set_data('GET', $_GET);
            $applicationBottomExtenderComponent->init_page();
    
            $this->coo_mn_data_container = MainFactory::create_object('MenuBoxesContentControl',
                                                                    [
                                                                        $GLOBALS['coo_template_control'],
                                                                        $applicationBottomExtenderComponent->get_page()
                                                                    ]);
            $this->coo_mn_data_container->set_('account_type', $_SESSION['account_type'] == '0' ? '0' : null);
            $this->coo_mn_data_container->set_('c_path', $GLOBALS['cPath']);
            $this->coo_mn_data_container->set_('category_id', $GLOBALS['cID'] ?? 0);
            $this->coo_mn_data_container->set_('customer_id', $_SESSION['customer_id'] ?? null);
            $this->coo_mn_data_container->set_('request_type', $GLOBALS['request_type']);
            $this->coo_mn_data_container->set_('coo_product', $GLOBALS['product']);
            $this->coo_mn_data_container->set_('coo_xtc_price', $GLOBALS['xtPrice']);
            $this->coo_mn_data_container->proceed();
        }
        return $this->coo_mn_data_container;
    }
}
