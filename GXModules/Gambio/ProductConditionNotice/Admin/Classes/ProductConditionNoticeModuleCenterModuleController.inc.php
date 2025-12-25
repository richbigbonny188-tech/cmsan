<?php
/* --------------------------------------------------------------
   ProductConditionNoticeModuleCenterModuleController.inc.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use GXModules\Gambio\ProductConditionNotice\Services\ConditionNoticeConfigurationService;
use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeStaticServiceFactory;
use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeTextPhraseService;
use GXModules\Gambio\ProductConditionNotice\Services\WarrantyNoticeConfigurationService;

/**
 * Class ProductConditionNoticeModuleCenterModuleController
 */
class ProductConditionNoticeModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * @var ConditionNoticeConfigurationService
     */
    protected $conditionNoticeService;
    
    /**
     * @var WarrantyNoticeConfigurationService
     */
    protected $warrantyNoticeService;
    
    /**
     * @var ProductConditionNoticeTextPhraseService
     */
    protected $textPhraseService;
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    /**
     * @var string
     */
    protected $templatePath;
    
    /**
     * @var string
     */
    protected $assetsPath;
    
    /**
     * @var bool
     */
    protected $isDevMode;
    
    
    /**
     * @inheritDoc
     */
    protected function _init()
    {
        $this->conditionNoticeService = ProductConditionNoticeStaticServiceFactory::createConditionNoticeService();
        $this->warrantyNoticeService  = ProductConditionNoticeStaticServiceFactory::createWarrantyNoticeService();
        $this->textPhraseService      = ProductConditionNoticeStaticServiceFactory::createTextPhraseService();
        $this->languageProvider       = MainFactory::create(LanguageProvider::class,
                                                            StaticGXCoreLoader::getDatabaseQueryBuilder());
        
        $this->templatePath = 'Gambio/ProductConditionNotice/Admin/Html';
        $this->assetsPath   = DIR_WS_CATALOG . 'GXModules/Gambio/ProductConditionNotice/Build/Admin/Styles';
        $this->isDevMode    = file_exists(__DIR__ . '/../../../../../.dev-environment');
    }
    
    
    /**
     * @inheritDoc
     */
    public function actionDefault()
    {
        $title    = new NonEmptyStringType($this->textPhraseService->getTextPhrase('module_title'));
        $template = $this->getTemplateFile($this->templatePath . '/product_condition_notice_configuration.html');
        
        $data = MainFactory::create('KeyValueCollection', [
            'pageToken'     => $_SESSION['coo_page_token']->generate_token(),
            'configuration' => [
                'show_condition_notice'         => $this->conditionNoticeService->isConditionNoticeEnabled(),
                'condition_notice_is_mandatory' => $this->conditionNoticeService->isConditionNoticeMandatory(),
                'condition_notice_text'         => $this->conditionNoticeService->getAllConditionNoticeTexts(),
                'show_warranty_notice'          => $this->warrantyNoticeService->isWarrantyNoticeEnabled(),
                'warranty_notice_is_mandatory'  => $this->warrantyNoticeService->isWarrantyNoticeMandatory(),
                'warranty_notice_text'          => $this->warrantyNoticeService->getAllWarrantyNoticeTexts(),
            ],
        ]);
        
        $assets = MainFactory::create('AssetCollection');
        $assets->add(MainFactory::create('Asset', 'admin_buttons.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'includes/ckeditor/ckeditor.js'));
        $assets->add(MainFactory::create('Asset',
                                         $this->assetsPath . '/product_condition_notice'
                                         . ($this->isDevMode ? '.min' : '') . '.css'));
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    /**
     * @inheritDoc
     */
    public function actionStoreConfiguration()
    {
        $this->_validatePageToken();
        
        if ($this->_getPostData('show_condition_notice') === 'true') {
            $this->conditionNoticeService->enableConditionNotice();
        } else {
            $this->conditionNoticeService->disableConditionNotice();
        }
        
        if ($this->_getPostData('condition_notice_is_mandatory') === 'true') {
            $this->conditionNoticeService->setConditionNoticeAsMandatory();
        } else {
            $this->conditionNoticeService->setConditionNoticeAsNotMandatory();
        }
        
        $this->conditionNoticeService->setConditionNoticeTexts($this->_getPostData('condition_notice_text'));
        
        if ($this->_getPostData('show_warranty_notice') === 'true') {
            $this->warrantyNoticeService->enableWarrantyNotice();
        } else {
            $this->warrantyNoticeService->disableWarrantyNotice();
        }
        
        if ($this->_getPostData('warranty_notice_is_mandatory') === 'true') {
            $this->warrantyNoticeService->setWarrantyNoticeAsMandatory();
        } else {
            $this->warrantyNoticeService->setWarrantyNoticeAsNotMandatory();
        }
        
        $this->warrantyNoticeService->setWarrantyNoticeTexts($this->_getPostData('warranty_notice_text'));
        
        $GLOBALS['messageStack']->add_session($this->textPhraseService->getTextPhrase('message_stack_configuration_stored'),
                                              'success');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=ProductConditionNoticeModuleCenterModule'));
    }
}