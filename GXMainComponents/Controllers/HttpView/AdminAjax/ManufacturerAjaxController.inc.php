<?php

/* --------------------------------------------------------------
  ManufacturerAjaxController.inc.php 2022-08-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class ManufacturerAjaxController
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class ManufacturerAjaxController extends AdminHttpViewController
{
    /**
     * @var \ManufacturerReadService
     */
    protected $manufacturerReadService;
    
    /**
     * @var \ManufacturerWriteService
     */
    protected $manufacturerWriteService;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * Initialization of Manufacturer controller
     */
    public function init()
    {
        $this->languageProvider         = MainFactory::create('LanguageProvider',
                                                              StaticGXCoreLoader::getDatabaseQueryBuilder());
        $this->manufacturerReadService  = StaticGXCoreLoader::getService('ManufacturerRead');
        $this->manufacturerWriteService = StaticGXCoreLoader::getService('ManufacturerWrite');
    }
    
    
    /**
     * Creates an manufacturer entity in the database.
     *
     * @return bool
     */
    public function actionSave()
    {
        $result = $this->_storeManufacturer($this->manufacturerWriteService->createManufacturer());
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * Creates manufacturers data from post data.
     * manufacturer_urls from $_POST parameter are set by this method.
     *
     * @param \ManufacturerInterface $manufacturer
     *
     * @return array
     */
    protected function _storeManufacturer(ManufacturerInterface $manufacturer)
    {
        try {
            $manufacturer->setName(new NonEmptyStringType(strip_tags(stripslashes($this->_getPostData('manufacturer_name')))));
            
            $this->_storeManufacturerUrls($manufacturer);
            $this->_storeManufacturerImage($manufacturer);
            $this->_storeManufacturerImageName($manufacturer);
            $this->_deleteImageCheckbox($manufacturer);
            $this->manufacturerWriteService->save($manufacturer);
            
            $result = [
                'renamed' => $this->_isImageNameChanged($manufacturer),
                'success' => true
            ];
        } catch (Exception $e) {
            $result = [
                'success' => false,
                'renamed' => false,
                'msg'     => $e->getMessage()
            ];
        }
        
        return $result;
    }
    
    
    /**
     * Stores all manufacturer urls with language id.
     *
     * @param \ManufacturerInterface $manufacturer
     *
     * @return $this
     */
    protected function _storeManufacturerUrls(ManufacturerInterface $manufacturer)
    {
        $manufacturerUrls  = $this->_getPostData('manufacturer_urls') ? : [];
        $languageIds       = $this->languageProvider->getIds()->getIntArray();
        $defaultLanguageId = $this->languageProvider->getDefaultLanguageId();
        foreach ($languageIds as $languageId) {
            $url = $manufacturerUrls[(string)$languageId] ? : $manufacturerUrls[(string)$defaultLanguageId];
            $manufacturer->setUrl(new StringType(strip_tags($url)),
                                  $this->languageProvider->getCodeById(new IdType($languageId)));
        }
        
        return $this;
    }
    
    
    /**
     * Stores an manufacturer image or replace one.
     *
     * @param \ManufacturerInterface $manufacturer
     *
     * @return $this
     */
    protected function _storeManufacturerImage(ManufacturerInterface $manufacturer)
    {
        $imageName    = $this->manufacturerWriteService->unifyFilename(new FilenameStringType(strip_tags($_FILES['manufacturer_logo']['name'] ?? '')));
        $filesTmpName = strip_tags($_FILES['manufacturer_logo']['tmp_name'] ?? '');
        
        if (array_key_exists('manufacturer_logo', $_FILES) && $filesTmpName !== '' && $imageName !== '') {
            $existingFilesTmpName = new ExistingFile(new NonEmptyStringType($filesTmpName));
            
            if ($manufacturer->getId() === 0) {
                $this->_setImageToManufacturer($manufacturer, $existingFilesTmpName, $imageName);
            } else {
                $this->_deleteImageIfExists($manufacturer);
                
                $this->_setImageToManufacturer($manufacturer, $existingFilesTmpName, $imageName);
            }
        }
        
        return $this;
    }
    
    
    /**
     * Stores manufacturer image name if set.
     *
     * @param \ManufacturerInterface $manufacturer
     *
     * @return $this
     */
    protected function _storeManufacturerImageName(ManufacturerInterface $manufacturer)
    {
        $manufacturerLogoName = strip_tags($this->_getPostData('manufacturer_logo'));
        
        if ($manufacturerLogoName !== null && $manufacturerLogoName !== ''
            && !(strpos($manufacturerLogoName, 'manufacturers/') !== false)) {
            $manufacturer->setImage(new StringType('manufacturers/' . $manufacturerLogoName));
        }
        
        return $this;
    }
    
    
    /**
     * If checkbox are checked, delete the manufacturer image.
     *
     * @param \ManufacturerInterface $manufacturer
     *
     * @return $this
     */
    protected function _deleteImageCheckbox(ManufacturerInterface $manufacturer)
    {
        $filesTmpName = strip_tags($_FILES['manufacturer_logo']['tmp_name'] ?? '');
        $isPostFile   = strip_tags($this->_getPostData('manufacturer_file'));
        
        if ($this->_getPostData('manufacturer_checkbox') === 'true'
            && ($filesTmpName === ''
                || $isPostFile === 'false')) {
            if ($isPostFile === 'true') {
                $this->_removeImage();
                $manufacturer->setImage(new StringType(''));
            }
            
            if ($isPostFile === 'false') {
                $manufacturer->setImage(new StringType(''));
            }
        }
        
        return $this;
    }
    
    
    /**
     * Checks if the name of the image has changed.
     *
     * @param \ManufacturerInterface $manufacturer
     *
     * @return boolean
     */
    protected function _isImageNameChanged(ManufacturerInterface $manufacturer)
    {
        
        if (array_key_exists('manufacturer_logo', $_FILES)
            && strip_tags($_FILES['manufacturer_logo']['tmp_name']) !== '') {
            
            return basename($manufacturer->getImage())
                   === strip_tags($_FILES['manufacturer_logo']['name']) ? false : true;
        }
        
        return false;
    }
    
    
    /**
     * Set an Image to an Manufacturer after saving it in the Filesystem.
     *
     * @param \ManufacturerInterface $manufacturer
     * @param \ExistingFile          $existingFilesTmpName
     * @param \FilenameStringType    $imageName
     *
     * @return $this
     */
    protected function _setImageToManufacturer(
        ManufacturerInterface $manufacturer,
        ExistingFile $existingFilesTmpName,
        FilenameStringType $imageName
    ) {
        $this->manufacturerWriteService->saveImage($existingFilesTmpName, $imageName);
        $manufacturer->setImage(new StringType('manufacturers/' . $imageName->asString()));
        
        return $this;
    }
    
    
    /**
     * Deletes image from manufacturer if set.
     *
     * @param \ManufacturerInterface $manufacturer
     *
     * @return $this
     */
    protected function _deleteImageIfExists(ManufacturerInterface $manufacturer)
    {
        if ($manufacturer->getImage() !== '') {
            $this->manufacturerWriteService->deleteImage(new IdType($manufacturer->getId()));
        }
        
        return $this;
    }
    
    
    /**
     * Removes an manufacturers image in the database and filesystem if image is set.
     *
     * @return $this
     */
    public function _removeImage()
    {
        $manufacturer = $this->_manufacturerById('post');
        
        if ($manufacturer->getImage() !== '') {
            $this->manufacturerWriteService->deleteImage(new IdType($manufacturer->getId()));
        }
        
        return $this;
    }
    
    
    /**
     *
     *
     * @param string $type
     *
     * @return \ManufacturerInterface
     */
    protected function _manufacturerById($type = 'get')
    {
        $id = ($type === 'post') ? $this->_getPostData('id') : $this->_getQueryParameter('id');
        
        return $this->manufacturerReadService->getById(new IdType($id));
    }
    
    
    /**
     * Gets all manufacturers entity's from database.
     *
     * @return bool
     */
    public function actionGetData()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   $this->_serializeManufacturerCollection($this->manufacturerReadService->getAll()));
    }
    
    
    /**
     * Serializes manufacturer collections.
     *
     * @param \ManufacturerCollection $manufacturerCollection Manufacturer collection to be serialized.
     *
     * @return array Serialized manufacturer collection array.
     */
    protected function _serializeManufacturerCollection(ManufacturerCollection $manufacturerCollection)
    {
        $data = [];
        foreach ($manufacturerCollection->getArray() as $manufacturer) {
            $data[] = $this->_serializeManufacturer($manufacturer);
        }
        
        return $data;
    }
    
    
    /**
     * Serializes manufacturer entities.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer entity to be serialized.
     *
     * @return array Serialized manufacturer array.
     */
    protected function _serializeManufacturer(ManufacturerInterface $manufacturer)
    {
        return [
            'id'           => $manufacturer->getId(),
            'name'         => $manufacturer->getName(),
            'imagePath'    => '../images/' . $manufacturer->getImage(),
            'image'        => $manufacturer->getImage(),
            'dateAdded'    => $manufacturer->getDateAdded()->format('d.m.Y H:i'),
            'lastModified' => $manufacturer->getLastModified()->format('d.m.Y H:i'),
            'urls'         => $manufacturer->getUrls(),
            'languageIds'  => $this->_langIdsByLangCode(array_keys($manufacturer->getUrls()))
        ];
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
     * Gets an manufacturers entity from database by id.
     *
     * @return bool
     */
    public function actionGetById()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   $this->_serializeManufacturer($this->_manufacturerById()));
    }
    
    
    /**
     * Updates an manufacturers entity in the database.
     *
     * @return bool
     */
    public function actionUpdate()
    {
        $result = $this->_storeManufacturer($this->manufacturerReadService->getById(new IdType($this->_getPostData('id'))));
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * Removes an manufacturers entity in the database.
     *
     * @return bool
     */
    public function actionRemove()
    {
        $this->_removeImage()->manufacturerWriteService->delete($this->_manufacturerById('post'));
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
    
    
    /**
     * Deserialize manufacturer entities.
     *
     * @param string $manufacturerJson Manufacturer entity as json string.
     *
     * @param null   $id
     *
     * @return \Manufacturer Deserialize manufacturer entity.
     */
    protected function _deserializeManufacturer($manufacturerJson, $id = null)
    {
        $manufacturerData = json_decode($manufacturerJson, true);
        $manufacturer     = $id ? $this->manufacturerReadService->getById(new IdType($id)) : $this->manufacturerWriteService->createManufacturer();
        
        $manufacturer->setName(new StringType($manufacturerData['name']))->setImage(new StringType('manufacturers/'
                                                                                                   . $manufacturerData['image']));
        
        foreach ($manufacturerData['urls'] as $languageCode => $url) {
            $manufacturer->setUrl(new StringType($url), new LanguageCode(new StringType($languageCode)));
        }
        
        return $manufacturer;
    }
}
