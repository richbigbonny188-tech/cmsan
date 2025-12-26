<?php

/* --------------------------------------------------------------
 VPEAjaxController.inc.php 2023-03-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

MainFactory::load_class('AdminHttpViewController');

/**
 * Class VPEAjaxController
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class VPEAjaxController extends AdminHttpViewController
{
    /**
     * @var \VPEReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var \VPEWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * Initializes the vpe ajax controller.
     *
     * Loads and sets the required dependencies as properties.
     * (VPEReadService, VPEWriteService and LanguageProvider)
     */
    public function init()
    {
        $this->readService      = StaticGXCoreLoader::getService('VPERead');
        $this->writeService     = StaticGXCoreLoader::getService('VPEWrite');
        $this->languageProvider = MainFactory::create('LanguageProvider',
                                                      StaticGXCoreLoader::getDatabaseQueryBuilder());
    }
    
    
    /**
     * Provides all vpe entities in json format for the listing.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionGetData()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   [
                                       'data'         => $this->_serializeVpeCollection($this->readService->getAll()),
                                       'languageCode' => $this->languageProvider->getCodeById(new IdType($_SESSION['languages_id']))
                                           ->asString()
                                   ]);
    }
    
    
    /**
     * Provides a single vpe entity in json format.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionGetById()
    {
        return MainFactory::create('JsonHttpControllerResponse', $this->_serializeVpe($this->_vpeById()));
    }
    
    
    /**
     * Stores a new vpe entity in the database.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionStore()
    {
        $this->_storeVpe($this->writeService->createVPE());
        
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Updates a vpe entity in the database.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionEdit()
    {
        $this->_storeVpe($this->_vpeById('post'));
        
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Removes a vpe entity from the database.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionRemove()
    {
        $this->writeService->delete($this->_vpeById('post'));
        
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Stores a vpe entity.
     * Names from $_POST parameter are set by this method.
     *
     * @param \VPEInterface $vpe
     *
     * @return $this|\VPEAjaxController Same instance for chained method calls.
     */
    protected function _storeVpe(VPEInterface $vpe)
    {
        if ($this->_getPostData('default')) {
            $vpe->setDefault(new BoolType(true));
        }
        
        $languageIds       = $this->languageProvider->getIds()->getIntArray();
        $defaultLanguageId = $this->languageProvider->getDefaultLanguageId();
        $vpeNames          = $this->_getPostData('name');
        foreach ($languageIds as $languageId) {
            $vpeName = $vpeNames[(string)$languageId] ? : $vpeNames[(string)$defaultLanguageId];
    
            if ($vpeName === '') {
                foreach ($vpeNames as $name) {
                    if ($name !== '') {
                        $vpeName = $name;
                
                        break;
                    }
                }
            }
            
            $vpe->setName(new StringType($vpeName), $this->languageProvider->getCodeById(new IdType($languageId)));
        }
        $this->writeService->save($vpe);
        
        if ($this->_getPostData('isDefault') && !$this->_getPostData('default')) {
            $query = 'UPDATE `gx_configurations` SET `value` = "" WHERE `key` = "configuration/DEFAULT_PRODUCTS_VPE_ID"';
            xtc_db_query($query);
        }
        
        return $this;
    }
    
    
    /**
     * Returns a vpe entity.
     *
     * @param string $type Determine if the vpe entity id should be accessed through GET or POST data.
     *
     * @return \VPEInterface Expected vpe entity.
     */
    protected function _vpeById($type = 'get')
    {
        $id    = ($type === 'get') ? $this->_getQueryParameter('id') : $this->_getPostData('id');
        $vpeId = new IdType((int)$id);
        try {
            return $this->readService->getById($vpeId);
        } catch (EntityNotFoundException $e) {
            $result = new VPE(new EditableKeyValueCollection([]));
            $result->setId($vpeId);
            
            return $result;
        }
    }
    
    
    /**
     * Converts an array with language codes to an array with codes as key and the language id as value.
     *
     * @param array $languageCodes Array that contains the language codes.
     *
     * @return array Format: [$languageCode => $languageId, (…)].
     */
    protected function _langIdsByLangCode(array $languageCodes)
    {
        $data = [];
        
        foreach ($languageCodes as $languageCode) {
            $data[$languageCode] = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
        }
        
        return $data;
    }
    
    
    /**
     * Serializes a vpe entity.
     *
     * @param \VPEInterface $vpe VPE Entity to be serialized.
     *
     * @return array Array that contains the vpe entity information.
     */
    protected function _serializeVpe(VPEInterface $vpe)
    {
        $adminLanguageCodes = array_map(function ($langCode) {
            return $langCode->asString();
        },
            $this->languageProvider->getAdminCodes()->getArray());
        $vpeNames           = array_filter($vpe->getNames(),
            function ($langCode) use ($adminLanguageCodes) {
                return in_array($langCode, $adminLanguageCodes, true);
            },
                                           ARRAY_FILTER_USE_KEY);
        
        return [
            'id'          => $vpe->getId(),
            'names'       => $vpeNames,
            'default'     => $vpe->isDefault(),
            'languageIds' => $this->_langIdsByLangCode(array_keys($vpe->getNames()))
        ];
    }
    
    
    /**
     * Serializes a vpe collection.
     *
     * @param \VPECollection $collection
     *
     * @return array Array that contains information about all vpe entities.
     */
    protected function _serializeVpeCollection(VPECollection $collection)
    {
        $data = [];
        foreach ($collection->getArray() as $vpe) {
            $data[] = $this->_serializeVpe($vpe);
        }
        
        return $data;
    }
}
