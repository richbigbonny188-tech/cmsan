<?php

/* --------------------------------------------------------------
 QuantityUnitAjaxController.inc.php 2023-03-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

MainFactory::load_class('AdminHttpViewController');

/**
 * Class QuantityUnitAjaxController
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class QuantityUnitAjaxController extends AdminHttpViewController
{
    /**
     * @var \QuantityUnitReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var \QuantityUnitWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * Initializes the quantity unit ajax controller.
     *
     * Loads and sets the required dependencies as properties.
     * (QuantityUnitReadService, QuantityUnitWriteService and LanguageProvider)
     */
    public function init()
    {
        $this->readService      = StaticGXCoreLoader::getService('QuantityUnitRead');
        $this->writeService     = StaticGXCoreLoader::getService('QuantityUnitWrite');
        $this->languageProvider = MainFactory::create('LanguageProvider',
                                                      StaticGXCoreLoader::getDatabaseQueryBuilder());
    }
    
    
    /**
     * Provides all quantity unit entities in json format for the listing.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionGetData()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   [
                                       'data'         => $this->_serializeQuantityUnitCollection($this->readService->getAll()),
                                       'languageCode' => $this->languageProvider->getCodeById(new IdType($_SESSION['languages_id']))
                                           ->asString()
                                   ]);
    }
    
    
    /**
     * Provides a single quantity unit entity in json format.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionGetById()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   $this->_serializeQuantityUnit($this->_quantityUnitById()));
    }
    
    
    /**
     * Stores a new quantity unit entity in the database.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionStore()
    {
        $this->_storeQuantityUnit($this->writeService->createQuantityUnit());
        
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Updates a quantity unit entity in the database.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionEdit()
    {
        $this->_storeQuantityUnit($this->readService->getById(new IdType($this->_getPostData('id'))));
        
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Removes a quantity unit entity from the database.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionRemove()
    {
        $this->writeService->delete($this->_quantityUnitById('post'));
        
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Stores a quantity unit entity.
     * Names from $_POST parameter are set by this method.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit entity to be stored.
     *
     * @return $this|\QuantityUnitAjaxController Same instance for chained method calls.
     */
    protected function _storeQuantityUnit(QuantityUnitInterface $quantityUnit)
    {
        $quantityNames     = $this->_getPostData('name');
        $languageIds       = $this->languageProvider->getIds()->getIntArray();
        $defaultLanguageId = $this->languageProvider->getDefaultLanguageId();
        foreach ($languageIds as $languageId) {
            $quantityName = $quantityNames[(string)$languageId] ? : $quantityNames[(string)$defaultLanguageId];
            
            if ($quantityName === '') {
                foreach ($quantityNames as $name) {
                    if ($name !== '') {
                        $quantityName = $name;
                        
                        break;
                    }
                }
            }
            
            $quantityUnit->setName(new StringType($quantityName),
                                   $this->languageProvider->getCodeById(new IdType($languageId)));
        }
        $this->writeService->save($quantityUnit);
        
        return $this;
    }
    
    
    /**
     * Returns a quantity unit entity.
     *
     * @param string $type Determine if the quantity unit entity id should be accessed through GET or POST data.
     *
     * @return \QuantityUnitInterface Expected quantity unit entity.
     */
    protected function _quantityUnitById($type = 'get')
    {
        if ($type === 'post') {
            return $this->readService->getById(new IdType($this->_getPostData('id')));
        }
        
        return $this->readService->getById(new IdType($this->_getQueryParameter('id')));
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
     * Serializes a quantity unit entity.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit entity to be serialized.
     *
     * @return array Array that contains the quantity unit entity information.
     */
    protected function _serializeQuantityUnit(QuantityUnitInterface $quantityUnit)
    {
        $adminLanguageCodes = array_map(function ($langCode) {
            return $langCode->asString();
        },
            $this->languageProvider->getAdminCodes()->getArray());
        $quantityUnitNames  = array_filter($quantityUnit->getNames(),
            function ($langCode) use ($adminLanguageCodes) {
                return in_array($langCode, $adminLanguageCodes, true);
            },
                                           ARRAY_FILTER_USE_KEY);
        
        return [
            'id'          => $quantityUnit->getId(),
            'names'       => $quantityUnitNames,
            'languageIds' => $this->_langIdsByLangCode(array_keys($quantityUnit->getNames()))
        ];
    }
    
    
    /**
     * Serializes a quantity unit collection.
     *
     * @param \QuantityUnitCollection $collection
     *
     * @return array Array that contains information about all quantity unit entities.
     */
    protected function _serializeQuantityUnitCollection(QuantityUnitCollection $collection)
    {
        $data = [];
        foreach ($collection->getArray() as $quantityUnit) {
            $data[] = $this->_serializeQuantityUnit($quantityUnit);
        }
        
        return $data;
    }
}
