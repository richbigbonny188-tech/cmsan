<?php
/* --------------------------------------------------------------
   AdminErrorPageController.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Admin\Modules\UserFriendlyErrorPage\Services\UserFriendlyErrorPageService;
use GuzzleHttp\Client;

/**
 * Class AdminErrorPageController
 */
class AdminErrorPageController extends AdminHttpViewController
{
    /**
     * @var UserFriendlyErrorPageService
     */
    protected $userFriendlyErrorPageService;
    
    
    public function init(): void
    {
        $this->userFriendlyErrorPageService = LegacyDependencyContainer::getInstance()
            ->get(UserFriendlyErrorPageService::class);
    }
    
    
    /**
     * Renders configuration page for custom error pages
     *
     * @return AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        $configurationStorage = MainFactory::create('ConfigurationStorage', 'error_pages');
        $languageTextManager  = MainFactory::create('LanguageTextManager', 'error_pages', $_SESSION['languages_id']);
        
        $title    = new NonEmptyStringType($languageTextManager->get_text('module_title'));
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/error_pages/configuration.html'));
        
        $data   = [
            'userFriendlyErrorHandling' => $this->userFriendlyErrorPageService->getUserFriendlyErrorPageActiveState(UserFriendlyErrorPageService::UNEXPECTED_ERROR_TYPE),
            'customPageNotFound'        => $this->userFriendlyErrorPageService->getUserFriendlyErrorPageActiveState(UserFriendlyErrorPageService::PAGE_NOT_FOUND_TYPE),
            'notFoundHtml'              => json_decode($configurationStorage->get('notFoundHtml'), true),
            'errorHtml'                 => json_decode($configurationStorage->get('errorHtml'), true),
        ];
        $data   = MainFactory::create('KeyValueCollection', $data);
        $assets = $this->_getAssetsArray();
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    /**
     * Returns the assets as an array.
     *
     * @return array Returns the assets as an array.
     */
    protected function _getAssetsArray()
    {
        $assets = MainFactory::create('AssetCollection');
        $assets->add(MainFactory::create('Asset', 'admin_buttons.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'includes/ckeditor/ckeditor.js'));
        
        return $assets;
    }
    
    
    public function actionSaveConfiguration()
    {
        $this->createMainCssIfMissing();
        
        $userFriendlyErrorHandlingActivated = $this->_getPostData('userFriendlyErrorHandling') === 'on';
        $customPageNotFoundActivated        = $this->_getPostData('customPageNotFound') === 'on';
        
        $configurationStorage = MainFactory::create('ConfigurationStorage', 'error_pages');
        $configurationStorage->set('notFoundHtml', json_encode($this->_getPostData('notFoundHtml')));
        $configurationStorage->set('errorHtml', json_encode($this->_getPostData('errorHtml')));
        
        $languageHelper      = MainFactory::create('LanguageHelper', StaticGXCoreLoader::getDatabaseQueryBuilder());
        $activeLanguageCodes = $languageHelper->getActiveLanguageCodes();
        foreach ($activeLanguageCodes as $languageCode) {
            $this->userFriendlyErrorPageService->storeUserFriendlyErrorPage(UserFriendlyErrorPageService::PAGE_NOT_FOUND_TYPE,
                                                                            $languageCode->asString(),
                                                                            $this->_getPostData('notFoundHtml')[$languageCode->asString()]);
            $this->userFriendlyErrorPageService->storeUserFriendlyErrorPage(UserFriendlyErrorPageService::UNEXPECTED_ERROR_TYPE,
                                                                            $languageCode->asString(),
                                                                            $this->_getPostData('errorHtml')[$languageCode->asString()]);
        }
        
        $this->userFriendlyErrorPageService->setUserFriendlyErrorPageActiveState(UserFriendlyErrorPageService::PAGE_NOT_FOUND_TYPE,
                                                                                 $customPageNotFoundActivated);
        $this->userFriendlyErrorPageService->setUserFriendlyErrorPageActiveState(UserFriendlyErrorPageService::UNEXPECTED_ERROR_TYPE,
                                                                                 $userFriendlyErrorHandlingActivated);
        
        return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=AdminErrorPage'));
    }
    
    
    /**
     * Requesting the main css file via curl to create it, if it doesn't exist
     */
    protected function createMainCssIfMissing(): void
    {
        $mainCssPath = DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemePath() . 'styles/system/';
        $mainCssPath .= file_exists(DIR_FS_CATALOG . '.dev-environment') ? 'main.css' : 'main.min.css';
        if (!file_exists($mainCssPath)) {
            $dynCssUrl = HTTP_SERVER . DIR_WS_CATALOG . StaticGXCoreLoader::getThemeControl()->getDynamicCssFilePath();
            $curl      = new Client();
            $curl->request('GET', $dynCssUrl);
        }
    }
}
