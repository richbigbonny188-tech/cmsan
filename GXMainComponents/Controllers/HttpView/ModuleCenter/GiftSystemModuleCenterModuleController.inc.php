<?php
/* --------------------------------------------------------------
   GiftSystemModuleCenterModuleController.inc.php 2020-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class GiftSystemModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /** @var LanguageTextManager */
    protected $text;
    
    /** @var GiftVouchersConfigurationStorage */
    protected $configuration;
    
    
    public function actionDefault()
    {
        $title     = new NonEmptyStringType($this->text->get_text('config_page_title'));
        $template  = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN . '/html/content/gift_vouchers_config.html'));
        $dataArray = [
            'pageToken'                  => $_SESSION['coo_page_token']->generate_token(),
            'action_save_configuration'  => xtc_href_link('admin.php',
                                                          'do=GiftSystemModuleCenterModule/SaveConfiguration'),
            'languageCode'               => MainFactory::create('LanguageCode',
                                                                new StringType($_SESSION['language_code'])),
            'releaseOrderStatuses'       => $this->configuration->get('releaseOrderStatuses'),
            'securityCodeLength'         => $this->configuration->get('securityCodeLength'),
            'newSignupGiftVoucherAmount' => $this->configuration->get('newSignupGiftVoucherAmount'),
            'newSignupDiscountCoupon'    => $this->configuration->get('newSignupDiscountCoupon'),
        ];
        
        /** @var OrderStatusService $ordersStatusService */
        $ordersStatusService        = StaticGXCoreLoader::getService('OrderStatus');
        $orderStatuses              = $ordersStatusService->findAll();
        $dataArray['orderStatuses'] = $orderStatuses;
        
        $data   = MainFactory::create('KeyValueCollection', $dataArray);
        $assets = MainFactory::create('AssetCollection', []);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets);
    }
    
    
    public function actionSaveConfiguration()
    {
        $this->_validatePageToken();
        
        $releaseOrderStatuses = $this->_getPostData('release_status');
        $releaseOrderStatuses = is_array($releaseOrderStatuses) ? $releaseOrderStatuses : [];
        $this->configuration->set('releaseOrderStatuses', $releaseOrderStatuses);
        $this->configuration->set('securityCodeLength', $this->_getPostData('security_code_length'));
        $this->configuration->set('newSignupGiftVoucherAmount', $this->_getPostData('new_signup_gift_voucher_amount'));
        $this->configuration->set('newSignupDiscountCoupon', $this->_getPostData('new_signup_discount_coupon'));
        
        $GLOBALS['messageStack']->add_session($this->text->get_text('configuration_saved'), 'info');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=GiftSystemModuleCenterModule'));
    }
    
    
    /**
     * @inheritDoc
     */
    protected function _init()
    {
        $this->text          = MainFactory::create('LanguageTextManager',
                                                   'gift_vouchers_config',
                                                   $_SESSION['languages_id']);
        $this->configuration = MainFactory::create('GiftVouchersConfigurationStorage');
        $this->pageTitle     = $this->text->get_text('config_page_title');
    }
    
    
}
