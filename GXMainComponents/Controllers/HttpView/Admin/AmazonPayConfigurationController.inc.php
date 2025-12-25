<?php
/* --------------------------------------------------------------
   AmazonPayConfigurationController.inc.php 2017-09-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AmazonPayConfigurationController extends AdminHttpViewController
{
    /**
     * @var $text \LanguageTextManager
     */
    protected $text;
    
    
    public function proceed(HttpContextInterface $httpContext)
    {
        $this->text = MainFactory::create('LanguageTextManager', 'amazonadvancedpayment', $_SESSION['languages_id']);
        parent::proceed($httpContext);
    }
    
    
    public function actionDefault()
    {
        $title     = new NonEmptyStringType($this->text->get_text('configuration_heading'));
        $template  = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                             . '/html/content/amazon_pay_configuration.html'));
        $amazonPay = MainFactory::create_object('AmazonAdvancedPayment');
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'form_action'                      => xtc_href_link('admin.php',
                                                                                            'do=AmazonPayConfiguration/SaveConfiguration'),
                                        'form_action_orderstatus'          => xtc_href_link('admin.php',
                                                                                            'do=AmazonPayConfiguration/SetOrderStatus'),
                                        'form_action_checkconnection'      => xtc_href_link('admin.php',
                                                                                            'do=AmazonPayConfiguration/CheckConnection'),
                                        'ipn_url'                          => HTTPS_CATALOG_SERVER . DIR_WS_CATALOG
                                                                              . 'request_port.php?module=AmazonIPN&key='
                                                                              . LogControl::get_secure_token(),
                                        'seller_id'                        => $amazonPay->seller_id,
                                        'aws_access_key'                   => $amazonPay->aws_access_key,
                                        'secret_key'                       => $amazonPay->secret_key,
                                        'mode'                             => $amazonPay->mode,
                                        'location'                         => $amazonPay->location,
                                        'button_color'                     => $amazonPay->button_color,
                                        'button_size'                      => $amazonPay->button_size,
                                        'hidden_button'                    => $amazonPay->hidden_button,
                                        'authorization_mode'               => $amazonPay->authorization_mode,
                                        'capture_mode'                     => $amazonPay->capture_mode,
                                        'erp_mode'                         => $amazonPay->erp_mode,
                                        'ipn_enabled'                      => $amazonPay->ipn_enabled,
                                        'orders_statuses'                  => $this->getOrdersStatuses(),
                                        'orders_status_auth_open'          => $amazonPay->orders_status_auth_open,
                                        'orders_status_auth_declined'      => $amazonPay->orders_status_auth_declined,
                                        'orders_status_auth_declined_hard' => $amazonPay->orders_status_auth_declined_hard,
                                        'orders_status_captured'           => $amazonPay->orders_status_captured,
                                        'orders_status_capture_failed'     => $amazonPay->orders_status_capture_failed,
                                    ]);
        
        $assets = MainFactory::create('AssetCollection',
                                      [
                                          MainFactory::create('Asset', 'amazonadvancedpayment.lang.inc.php'),
                                      ]);
        
        $ssoModuleInstalled = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        if ($ssoModuleInstalled) {
            $ssoConfig = MainFactory::create('SingleSignonConfigurationStorage');
            if ((bool)$ssoConfig->get('services/amazon/active') === false) {
                $GLOBALS['messageStack']->add($this->text->get_text('note_sso'), 'info');
            }
        }
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    public function actionSaveConfiguration()
    {
        $amazonPay = MainFactory::create_object('AmazonAdvancedPayment');
        
        $sellerId     = $this->_getPostData('seller_id');
        $awsAccessKey = $this->_getPostData('aws_access_key');
        $secretKey    = $this->_getPostData('secret_key');
        
        $quickConfig = stripslashes(trim($this->_getPostData('quickconfig')));
        if (!empty($quickConfig)) {
            $quickCredentials = json_decode($quickConfig, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (array_key_exists('merchant_id', $quickCredentials)
                    && array_key_exists('access_key', $quickCredentials)
                    && array_key_exists('secret_key', $quickCredentials)) {
                    $sellerId     = $quickCredentials['merchant_id'];
                    $awsAccessKey = $quickCredentials['access_key'];
                    $secretKey    = $quickCredentials['secret_key'];
                } else {
                    $GLOBALS['messageStack']->add_session($this->text->get_text('quickconfig_malformed')
                                                          . ' (required data missing)',
                                                          'warning');
                }
            } else {
                $GLOBALS['messageStack']->add_session($this->text->get_text('quickconfig_malformed') . ' ('
                                                      . json_last_error_msg() . ')',
                                                      'warning');
            }
        }
        
        $sellerId     = preg_replace('/[^[:alnum:]]*/', '', $sellerId);
        $awsAccessKey = preg_replace('/[^[:alnum:]]*/', '', $awsAccessKey);
        $secretKey    = preg_replace('/[^[:print:]]*/', '', $secretKey);
        $secretKey    = strip_tags($secretKey);
        
        $oldCredentials = implode('', [$amazonPay->seller_id, $amazonPay->aws_access_key, $amazonPay->secret_key]);
        $newCredentials = implode('', [$sellerId, $awsAccessKey, $secretKey]);
        if ($oldCredentials !== $newCredentials) {
            $orderReference = $amazonPay->check_credentials($sellerId, $awsAccessKey, $secretKey);
            if (isset($orderReference->Error, $orderReference->Error->Code)) {
                switch ((string)$orderReference->Error->Code) {
                    case 'InvalidOrderReferenceId':
                        $message = $this->text->get_text('credentials_changed');
                        $class   = 'info';
                        break;
                    case 'InvalidAccessKeyId':
                        $message = $this->text->get_text('credentials_invalid_access_key');
                        $class   = 'error';
                        break;
                    case 'SignatureDoesNotMatch':
                        $message = $this->text->get_text('credentials_invalid_signature');
                        $class   = 'error';
                        break;
                    case 'InvalidParameterValue':
                        $message = $this->text->get_text('credentials_invalid_merchant_id');
                        $class   = 'error';
                        break;
                    default:
                        $message = $this->text->get_text('credentials_check_failed');
                        $class   = 'warning';
                }
                $GLOBALS['messageStack']->add_session($message, $class);
            }
        }
        
        $amazonPay->seller_id                        = $sellerId;
        $amazonPay->aws_access_key                   = $awsAccessKey;
        $amazonPay->secret_key                       = $secretKey;
        $amazonPay->mode                             = $this->_getPostData('mode');
        $amazonPay->location                         = $this->_getPostData('location');
        $amazonPay->button_color                     = $this->_getPostData('button_color');
        $amazonPay->button_size                      = $this->_getPostData('button_size');
        $amazonPay->hidden_button                    = (bool)$this->_getPostData('hidden_button') ? '1' : '0';
        $amazonPay->authorization_mode               = $this->_getPostData('authorization_mode');
        $amazonPay->capture_mode                     = $this->_getPostData('capture_mode');
        $amazonPay->erp_mode                         = (bool)$this->_getPostData('erp_mode') ? '1' : '0';
        $amazonPay->ipn_enabled                      = (bool)$this->_getPostData('ipn_enabled') ? '1' : '0';
        $amazonPay->orders_status_auth_open          = (int)$this->_getPostData('orders_status_auth_open');
        $amazonPay->orders_status_auth_declined      = (int)$this->_getPostData('orders_status_auth_declined');
        $amazonPay->orders_status_auth_declined_hard = (int)$this->_getPostData('orders_status_auth_declined_hard');
        $amazonPay->orders_status_captured           = (int)$this->_getPostData('orders_status_captured');
        $amazonPay->orders_status_capture_failed     = (int)$this->_getPostData('orders_status_capture_failed');
        
        $GLOBALS['messageStack']->add_session($this->text->get_text('configuration_saved'), 'success');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=AmazonPayConfiguration'));
    }
    
    
    /**
     * Sets Amazon Pay default order status values; creates them if necessary.
     *
     * @return array|bool|\RedirectHttpControllerResponse
     */
    public function actionSetOrderStatus()
    {
        $amazonPay = MainFactory::create('AmazonAdvancedPayment');
        $amazonPay->useDefaultOrdersStatusConfiguration();
        $GLOBALS['messageStack']->add_session($this->text->get_text('using_default_orders_status_configuration'),
                                              'success');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=AmazonPayConfiguration'));
    }
    
    
    public function actionCheckConnection()
    {
        $amazonPay         = MainFactory::create('AmazonAdvancedPayment');
        $url               = $amazonPay::EP_OAP_DE_SANDBOX;
        $timeout           = 5;
        $connectionChecker = MainFactory::create('ConnectChecker');
        try {
            $connectionInfo = $connectionChecker->check_connect($url, $timeout);
            $GLOBALS['messageStack']->add_session($this->text->get_text('connection_ok') . ', URL: ' . $url,
                                                  'success');
        } catch (Exception $e) {
            $GLOBALS['messageStack']->add_session($this->text->get_text('connection_error') . ' - ' . $e->getMessage(),
                                                  'error');
        }
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=AmazonPayConfiguration'));
    }
    
    
    /* ============================= HELPERS ============================= */
    
    /**
     * Retrieves a array of order statuses (IDs and names as per current session language)
     *
     * @return array
     */
    protected function getOrdersStatuses()
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->where(['language_id' => $_SESSION['languages_id']]);
        $db->order_by('orders_status_name ASC');
        $orders_statuses_query = $db->get('orders_status');
        $orders_statuses       = $orders_statuses_query->result();
        
        return $orders_statuses;
    }
}
