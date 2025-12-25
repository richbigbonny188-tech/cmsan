<?php

/* --------------------------------------------------------------
  ManufacturerController.inc.php 2018-08-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

class ManufacturerController extends AdminHttpViewController
{
    /**
     * @var \LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var \ManufacturerReadService
     */
    protected $manufacturerReadService;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * Initialization of VPE controller
     */
    public function init()
    {
        $this->languageTextManager     = MainFactory::create('LanguageTextManager');
        $this->languageProvider        = MainFactory::create('LanguageProvider',
                                                             StaticGXCoreLoader::getDatabaseQueryBuilder());
        $this->manufacturerReadService = StaticGXCoreLoader::getService('ManufacturerRead');
    }
    
    
    /**
     * Default action of the manufacturer controller.
     * Provides necessary data to display the manufacturer listing.
     *
     * @return bool
     */
    public function actionDefault()
    {
        $languageIds = $this->languageProvider->getAdminIds()->getIntArray();
        $langIds     = [];
        $langDirs    = [];
        
        foreach ($languageIds as $languageId) {
            $id           = new IdType($languageId);
            $languageCode = $this->languageProvider->getCodeById($id);
            
            $idData = [
                'id'   => $languageId,
                'code' => $languageCode
            ];
            
            $langIds[] = $idData;
            
            $langDirs[$languageId]['directory'] = $this->languageProvider->getDirectoryById($id);
            $langDirs[$languageId]['icon']      = $this->languageProvider->getIconFilenameByCode($languageCode);
        }
        
        $data = [
            'manufacturers' => $this->manufacturerReadService->getAll(),
            'languages'     => $langIds,
            'langDirs'      => $langDirs,
            'langPath'      => DIR_WS_LANGUAGES,
            'languageCode'  => $this->languageProvider->getCodeById(new IdType($_SESSION['languages_id']))
        ];
        
        return $this->_getResponse($this->languageTextManager->get_text('HEADING_TITLE', 'manufacturers'),
                                   'overview',
                                   $data);
    }
    
    
    /**
     * Returns an admin layout http controller response.
     *
     * @param       $title
     * @param       $template
     * @param array $data
     *
     * @return bool
     */
    protected function _getResponse($title, $template, array $data)
    {
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   new NonEmptyStringType($title),
                                   new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN . 'html/content/manufacturer/'
                                                                           . $template . '.html')),
                                   new KeyValueCollection($data),
                                   new AssetCollection([
                                                           new Asset('manufacturers.lang.inc.php')
                                                       ]));
    }
}
