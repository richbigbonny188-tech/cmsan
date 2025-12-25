<?php

/* --------------------------------------------------------------
 VPEController.inc.php 2023-03-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

MainFactory::load_class('AdminHttpViewController');

/**
 * Class VPEController
 *
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class VPEController extends AdminHttpViewController
{
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * Initialization of VPE controller
     */
    public function init()
    {
        $this->languageProvider = MainFactory::create('LanguageProvider',
                                                      StaticGXCoreLoader::getDatabaseQueryBuilder());
    }
    
    
    /**
     * Default action of the vpe controller.
     * Provides necessary data to display the vpe listing.
     *
     * @return \AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        /** @var \VPEReadService $vpeReadService */
        
        $this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
        $vpeReadService      = StaticGXCoreLoader::getService('VPERead');
        $languageTextManager = MainFactory::create('LanguageTextManager');
        $languageTextManager->init_from_lang_file('products_vpe');
        
        $languageIds = $this->languageProvider->getAdminIds()->getIntArray();
        $langIds     = [];
        $langDirs    = [];
        
        foreach ($languageIds as $languageId) {
            $idData    = [
                'id'   => $languageId,
                'code' => $this->languageProvider->getCodeById(new IdType($languageId))
            ];
            $langIds[] = $idData;
            
            $langDirs[$languageId]['directory'] = $this->languageProvider->getDirectoryById(new IdType($languageId));
            $langDirs[$languageId]['icon']      = $this->languageProvider->getIconFilenameByCode($this->languageProvider->getCodeById(new IdType($languageId)));
        }
        
        $title = $languageTextManager->get_text($languageTextManager->get_text('HEADING_TITLE', 'products_vpe'));
        $data  = MainFactory::create('KeyValueCollection',
                                     [
                                         'languages'    => $langIds,
                                         'languageCode' => $this->languageProvider->getCodeById(new IdType($_SESSION['languages_id'])),
                                         'vpeEntities'  => $vpeReadService->getAll()->getArray(),
                                         'langPath'     => DIR_WS_LANGUAGES,
                                         'langDirs'     => $langDirs
                                     ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   new NonEmptyStringType($title),
                                   MainFactory::create('ExistingFile',
                                                       new NonEmptyStringType(DIR_FS_ADMIN
                                                                              . 'html/content/vpe/vpe.html')),
                                   $data,
                                   MainFactory::create('AssetCollection',
                                                       [
                                                           MainFactory::create('Asset', 'products_vpe.lang.inc.php')
                                                       ]));
    }
    
    
    protected function _getIconPathByLangId()
    {
        return function ($langId) {
            $id = new IdType($langId); // validates lang id and throw errors on invalid arguments
            
            return DIR_WS_LANGUAGES . $this->languageProvider->getDirectoryById($id) . '/admin/images/'
                   . $this->languageProvider->getIconFilenameByCode($this->languageProvider->getCodeById($id));
        };
    }
}
