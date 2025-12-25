<?php
/* --------------------------------------------------------------
  ProductJsonSerializer.inc.php 2022-05-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class ProductJsonSerializer
 *
 * This class will serialize and deserialize a Product entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class ProductJsonSerializer extends AbstractJsonSerializer
{
    /**
     * Serialize a Product object to a JSON string.
     *
     * @param ProductInterface $object         Object instance to be serialized.
     * @param bool             $encode         (optional) Whether to json_encode the result of the method (default
     *                                         true).
     *
     * @return string|array Returns the json encoded product (string) or an array that can be easily encoded into a
     *                      JSON string.
     * @throws InvalidArgumentException If the provided object type is invalid.
     * @throws RuntimeException Through the _serializeLanguageSpecificProperty.
     */
    public function serialize($object, $encode = true)
    {
        if (!is_a($object, 'ProductInterface')) {
            throw new InvalidArgumentException('Invalid argument provided, StoredProductInterface object required: '
                                               . get_class($object));
        }
        
        $product = [
            'id'                  => is_a($object, 'StoredProductInterface') ? $object->getProductId() : null,
            'isActive'            => $object->isActive(),
            'sortOrder'           => $object->getSortOrder(),
            'mainCategoryId'      => $object->getMainCategoryId(),
            'dateAdded'           => $object->getAddedDateTime()->format('Y-m-d H:i:s'),
            'dateAvailable'       => $object->getAvailableDateTime()->format('Y-m-d H:i:s'),
            'lastModified'        => $object->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
            'orderedCount'        => $object->getOrderedCount(),
            'productModel'        => $object->getProductModel(),
            'ean'                 => $object->getEan(),
            'price'               => $object->getPrice(),
            'discountAllowed'     => $object->getDiscountAllowed(),
            'taxClassId'          => $object->getTaxClassId(),
            'quantity'            => $object->getQuantity(),
            'weight'              => $object->getWeight(),
            'shippingCosts'       => $object->getShippingCosts(),
            'shippingTimeId'      => $object->getShippingTimeId(),
            'productTypeId'       => $object->getProductTypeId(),
            'manufacturerId'      => $object->getManufacturerId(),
            'quantityUnitId'      => $object->getQuantityUnitId(),
            'isFsk18'             => $object->isFsk18(),
            'isVpeActive'         => $object->isVpeActive(),
            'vpeId'               => $object->getVpeId(),
            'vpeValue'            => $object->getVpeValue(),
            'name'                => $this->_serializeLanguageSpecificProperty($object, 'name'),
            'description'         => $this->_serializeLanguageSpecificProperty($object, 'description'),
            'shortDescription'    => $this->_serializeLanguageSpecificProperty($object, 'shortDescription'),
            'keywords'            => $this->_serializeLanguageSpecificProperty($object, 'keywords'),
            'metaTitle'           => $this->_serializeLanguageSpecificProperty($object, 'metaTitle'),
            'metaDescription'     => $this->_serializeLanguageSpecificProperty($object, 'metaDescription'),
            'metaKeywords'        => $this->_serializeLanguageSpecificProperty($object, 'metaKeywords'),
            'url'                 => $this->_serializeLanguageSpecificProperty($object, 'url'), // deprecated
            'infoUrl'             => $this->_serializeLanguageSpecificProperty($object, 'infoUrl'),
            'urlKeywords'         => $this->_serializeLanguageSpecificProperty($object, 'urlKeywords'),
            'checkoutInformation' => $this->_serializeLanguageSpecificProperty($object, 'checkoutInformation'),
            'viewedCount'         => $this->_serializeLanguageSpecificProperty($object, 'viewedCount'),
            'images'              => $this->_serializeImages($object->getImageContainer()),
            'settings'            => $this->_serializeSettings($object->getSettings()),
            'addonValues'         => $this->_serializeAddonValues($object->getAddonValues()),
            'specialOfferId'      => $object->getSpecialOfferId()
        ];
        
        return ($encode) ? $this->jsonEncode($product) : $product;
    }
    
    
    /**
     * Serializes an array of ProductInterfaces to a JSON string.
     *
     * @param array $storedProducts The array of ProductInterfaces
     * @param bool  $encode         Indicates if the result should be returned JSON encoded.
     *
     * @return array|string Either a JSON string or the serialized array (depends on the $encode parameter)
     */
    public function serializeProductArray(array $storedProducts, $encode = true)
    {
        $productsArray = [];
        
        foreach ($storedProducts as $storedProduct) {
            $productsArray[] = $this->serialize($storedProduct, false);
        }
        
        return ($encode) ? $this->jsonEncode($productsArray) : $productsArray;
    }
    
    
    /**
     * Deserialize a Product JSON String.
     *
     * @param string $string     JSON string that contains the data of the product.
     * @param object $baseObject (optional) If provided, this will be the base object to be updated
     *                           and no new instance will be created.
     *
     * @return ProductInterface|\StoredProductInterface Returns the deserialized Product object.
     * @throws InvalidArgumentException If the argument is not a string or is empty.
     */
    public function deserialize($json, $baseObject = null)
    {
        if (!$baseObject) {
            $productSettings = MainFactory::create('ProductSettings');
            $product         = MainFactory::create('GXEngineProduct', $productSettings);
        } else {
            $product = $baseObject;
        }
        
        if (!is_object($json)) {
            $json = $this->decodeJson($json);
        }
        
        // Deserialize JSON String
        
        if (property_exists($json, 'isActive') && $json->isActive !== null) {
            $product->setActive(new BoolType($json->isActive));
        }
        
        if (property_exists($json, 'mainCategoryId') && $json->mainCategoryId !== null) {
            $product->setMainCategoryId(new IdType($json->mainCategoryId));
        }
        
        if (property_exists($json, 'sortOrder') && $json->sortOrder !== null) {
            $product->setSortOrder(new IntType($json->sortOrder));
        }
        
        if (property_exists($json, 'dateAdded') && $json->dateAdded !== null) {
            $product->setAddedDateTime(new EmptyDateTime($json->dateAdded));
        }
        
        if (property_exists($json, 'dateAvailable') && $json->dateAvailable !== null) {
            $product->setAvailableDateTime(new EmptyDateTime($json->dateAvailable));
        }
        
        if (property_exists($json, 'lastModified') && $json->lastModified !== null) {
            $product->setLastModifiedDateTime(new EmptyDateTime($json->lastModified));
        }
        
        if (property_exists($json, 'orderedCount') && $json->orderedCount !== null) {
            $product->setOrderedCount(new IntType($json->orderedCount));
        }
        
        if (property_exists($json, 'productModel') && $json->productModel !== null) {
            $product->setProductModel(new StringType($json->productModel));
        }
        
        if (property_exists($json, 'ean') && $json->ean !== null) {
            $product->setEan(new StringType($json->ean));
        }
        
        if (property_exists($json, 'price') && $json->price !== null) {
            $product->setPrice(new DecimalType($json->price));
        }
        
        if (property_exists($json, 'taxClassId') && $json->taxClassId !== null) {
            $product->setTaxClassId(new IdType($json->taxClassId));
        }
        
        if (property_exists($json, 'quantity') && $json->quantity !== null) {
            $product->setQuantity(new DecimalType($json->quantity));
        }
        
        if (property_exists($json, 'weight') && $json->weight !== null) {
            $product->setWeight(new DecimalType($json->weight));
        }
        
        if (property_exists($json, 'discountAllowed') && $json->discountAllowed !== null) {
            $product->setDiscountAllowed(new DecimalType($json->discountAllowed));
        }
        
        if (property_exists($json, 'shippingCosts') && $json->shippingCosts !== null) {
            $product->setShippingCosts(new DecimalType($json->shippingCosts));
        }
        
        if (property_exists($json, 'shippingTimeId') && $json->shippingTimeId !== null) {
            $product->setShippingTimeId(new IdType($json->shippingTimeId));
        }
        
        if (property_exists($json, 'productTypeId') && $json->productTypeId !== null) {
            $product->setProductTypeId(new IdType($json->productTypeId));
        }
        
        if (property_exists($json, 'manufacturerId') && $json->manufacturerId !== null) {
            $product->setManufacturerId(new IdType($json->manufacturerId));
        }
        
        if (property_exists($json, 'quantityUnitId') && $json->quantityUnitId !== null) {
            $product->setQuantityUnitId(new IdType($json->quantityUnitId));
        }
        
        if (property_exists($json, 'isFsk18') && $json->isFsk18 !== null) {
            $product->setFsk18(new BoolType($json->isFsk18));
        }
        
        if (property_exists($json, 'isVpeActive') && $json->isVpeActive !== null) {
            $product->setVpeActive(new BoolType($json->isVpeActive));
        }
        
        if (property_exists($json, 'vpeId') && $json->vpeId !== null) {
            $product->setVpeId(new IdType($json->vpeId));
        }
        
        if (property_exists($json, 'vpeValue') && $json->vpeValue !== null) {
            $product->setVpeValue(new DecimalType($json->vpeValue));
        }
        
        if (property_exists($json, 'images') && $json->images !== null) {
            $product->setImageContainer($this->_deserializeImages($json->images));
        }
        
        if (property_exists($json, 'name') && $json->name !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->name, 'name');
        }
        
        if (property_exists($json, 'description') && $json->description !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->description, 'description');
        }
        
        if (property_exists($json, 'shortDescription') && $json->shortDescription !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->shortDescription, 'shortDescription');
        }
        
        if (property_exists($json, 'keywords') && $json->keywords !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->keywords, 'keywords');
        }
        
        if (property_exists($json, 'metaKeywords') && $json->metaKeywords !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->metaKeywords, 'metaKeywords');
        }
        
        if (property_exists($json, 'metaTitle') && $json->metaTitle !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->metaTitle, 'metaTitle');
        }
        
        if (property_exists($json, 'metaDescription') && $json->metaDescription !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->metaDescription, 'metaDescription');
        }
        
        // deprecated
        if (property_exists($json, 'url') && $json->url !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->url, 'url');
        }
        
        if (property_exists($json, 'infoUrl') && $json->infoUrl !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->infoUrl, 'infoUrl');
        }
        
        if (property_exists($json, 'urlKeywords') && $json->urlKeywords !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->urlKeywords, 'urlKeywords');
        }
        
        if (property_exists($json, 'checkoutInformation') && $json->checkoutInformation !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->checkoutInformation, 'checkoutInformation');
        }
        
        if (property_exists($json, 'viewedCount') && $json->viewedCount !== null) {
            $this->_deserializeLanguageSpecificProperty($product, $json->viewedCount, 'viewedCount', 'IntType');
        }
        
        if (property_exists($json, 'settings') && $json->settings !== null) {
            $product->setSettings($this->_deserializeSettings($product->getSettings(), $json->settings));
        }
        
        if (property_exists($json, 'addonValues') && $json->addonValues !== null) {
            $productAddonValuesArray      = $this->_deserializeAddonValues($json->addonValues);
            $productAddonValuesCollection = MainFactory::create('EditableKeyValueCollection', $productAddonValuesArray);
            
            $product->addAddonValues($productAddonValuesCollection);
        }
        
        return $product;
    }
    
    
    /**
     * Decodes a JSON string and performs some basic validation.
     *
     * @param mixed $string The (potential) JSON string
     *
     * @return array The decoded JSON array
     */
    public function decodeJson($string)
    {
        if (!is_string($string) || empty($string)) {
            throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
        }
        
        $json = json_decode($string);
        
        // error for malformed json strings
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
        }
        
        return $json;
    }
    
    
    /**
     * Serialize Images
     *
     * @param \ProductImageContainerInterface $imageContainer
     *
     * @return array
     */
    protected function _serializeImages(ProductImageContainerInterface $imageContainer)
    {
        $images = [];
        
        if ($imageContainer->getPrimary()->getFilename() !== '') {
            // primary image
            $primaryImage = $imageContainer->getPrimary();
            $images[]     = [
                'filename'     => $primaryImage->getFilename(),
                'isPrimary'    => true,
                'isVisible'    => $primaryImage->isVisible(),
                'imageAltText' => $this->_serializeLanguageSpecificProperty($primaryImage, 'altText')
            ];
        }
        
        // additional images
        foreach ($imageContainer->getAdditionals()->getArray() as $additionalImage) {
            /** @var ProductImageInterface $additionalImage */
            $images[] = [
                'filename'     => $additionalImage->getFilename(),
                'isPrimary'    => false,
                'isVisible'    => $additionalImage->isVisible(),
                'imageAltText' => $this->_serializeLanguageSpecificProperty($additionalImage, 'altText')
            ];
        }
        
        return $images;
    }
    
    
    /**
     * Deserialize Images
     *
     * @param $json
     *
     * @return \ProductImageContainer
     *
     * @throws InvalidArgumentException Through "addAdditional"
     * @throws RuntimeException Through "_deserializeLanguageSpecificProperty"
     */
    protected function _deserializeImages($json)
    {
        $productImageContainer = MainFactory::create('ProductImageContainer');
        
        foreach ($json as $image) {
            $productImage = MainFactory::create('ProductImage', new FilenameStringType($image->filename));
            $this->_deserializeLanguageSpecificProperty($productImage, $image->imageAltText, 'altText');
            
            $productImage->setVisible(new BoolType((bool)$image->isVisible));
            
            if ($image->isPrimary) {
                $productImageContainer->setPrimary($productImage);
            } else {
                $productImageContainer->addAdditional($productImage);
            }
        }
        
        return $productImageContainer;
    }
    
    
    /**
     * Serialize Settings
     *
     * @param \ProductSettingsInterface $settings
     *
     * @return array
     */
    protected function _serializeSettings(ProductSettingsInterface $settings)
    {
        $databaseQueryBuilder   = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $customerStatusProvider = MainFactory::create('CustomerStatusProvider', $databaseQueryBuilder);
        $customerStatusGroups   = $customerStatusProvider->getCustomerStatusIds();
        
        $permissions = [];
        foreach ($customerStatusGroups as $groupId) {
            $permissions[] = [
                'id'          => (int)$groupId,
                'isPermitted' => $settings->isPermittedCustomerStatus(new IdType($groupId))
            ];
        }
        
        $serializedSettings = [
            'detailsTemplate'                   => $settings->getDetailsTemplate(),
            'optionsDetailsTemplate'            => $settings->getOptionsDetailsTemplate(),
            'optionsListingTemplate'            => $settings->getOptionsListingTemplate(),
            'showOnStartpage'                   => $settings->showOnStartpage(),
            'showQuantityInfo'                  => $settings->showQuantityInfo(),
            'showWeight'                        => $settings->showWeight(),
            'showPriceOffer'                    => $settings->showPriceOffer(),
            'showAddedDateTime'                 => $settings->showAddedDateTime(),
            'priceStatus'                       => $settings->getPriceStatus(),
            'minOrder'                          => $settings->getMinOrder(),
            'graduatedQuantity'                 => $settings->getGraduatedQuantity(),
            'onSitemap'                         => $settings->isSitemapEntry(),
            'sitemapPriority'                   => $settings->getSitemapPriority(),
            'sitemapChangeFrequency'            => $settings->getSitemapChangeFreq(),
            'propertiesDropdownMode'            => $settings->getPropertiesDropdownMode(),
            'startpageSortOrder'                => $settings->getStartpageSortOrder(),
            'showPropertiesPrice'               => $settings->showPropertiesPrice(),
            'propertiesCombisQuantityCheckMode' => $settings->getPropertiesCombisQuantityCheckMode(),
            'usePropertiesCombisShippingTime'   => $settings->usePropertiesCombisShippingTime(),
            'usePropertiesCombisWeight'         => $settings->usePropertiesCombisWeight(),
            'groupPermissions'                  => $permissions
        ];
        
        return $serializedSettings;
    }
    
    
    /**
     * Deserialize Settings
     *
     * @param \ProductSettingsInterface $settings
     * @param                           $json
     *
     * @return \ProductSettingsInterface
     */
    protected function _deserializeSettings(ProductSettingsInterface $settings, $json)
    {
        if ($json->detailsTemplate !== null) {
            $settings->setDetailsTemplate(new StringType($json->detailsTemplate));
        }
        
        if ($json->optionsDetailsTemplate !== null) {
            $settings->setOptionsDetailsTemplate(new StringType($json->optionsDetailsTemplate));
        }
        
        if ($json->optionsListingTemplate !== null) {
            $settings->setOptionsListingTemplate(new StringType($json->optionsListingTemplate));
        }
        
        if ($json->showOnStartpage !== null) {
            $settings->setShowOnStartpage(new BoolType($json->showOnStartpage));
        }
        
        if ($json->showQuantityInfo !== null) {
            $settings->setShowQuantityInfo(new BoolType($json->showQuantityInfo));
        }
        
        if ($json->showWeight !== null) {
            $settings->setShowWeight(new BoolType($json->showWeight));
        }
        
        if ($json->showPriceOffer !== null) {
            $settings->setShowPriceOffer(new BoolType($json->showPriceOffer));
        }
        
        if ($json->showAddedDateTime !== null) {
            $settings->setShowAddedDateTime(new BoolType($json->showAddedDateTime));
        }
        
        if ($json->priceStatus !== null) {
            $settings->setPriceStatus(new IntType($json->priceStatus));
        }
        
        if ($json->minOrder !== null) {
            $settings->setMinOrder(new DecimalType($json->minOrder));
        }
        
        if ($json->graduatedQuantity !== null) {
            $settings->setGraduatedQuantity(new DecimalType($json->graduatedQuantity));
        }
        
        if ($json->onSitemap !== null) {
            $settings->setSitemapEntry(new BoolType($json->onSitemap));
        }
        
        if ($json->sitemapPriority !== null) {
            $settings->setSitemapPriority(new StringType($json->sitemapPriority));
        }
        
        if ($json->sitemapChangeFrequency !== null) {
            $settings->setSitemapChangeFreq(new StringType($json->sitemapChangeFrequency));
        }
        
        if ($json->propertiesDropdownMode !== null) {
            $settings->setPropertiesDropdownMode(new StringType($json->propertiesDropdownMode));
        }
        
        if ($json->startpageSortOrder !== null) {
            $settings->setStartpageSortOrder(new IntType($json->startpageSortOrder));
        }
        
        if ($json->showPropertiesPrice !== null) {
            $settings->setShowPropertiesPrice(new BoolType($json->showPropertiesPrice));
        }
        
        if ($json->propertiesCombisQuantityCheckMode !== null) {
            $settings->setPropertiesCombisQuantityCheckMode(new IntType($json->propertiesCombisQuantityCheckMode));
        }
        
        if ($json->usePropertiesCombisShippingTime !== null) {
            $settings->setUsePropertiesCombisShippingTime(new BoolType($json->usePropertiesCombisShippingTime));
        }
        
        if ($json->usePropertiesCombisWeight !== null) {
            $settings->setUsePropertiesCombisWeight(new BoolType($json->usePropertiesCombisWeight));
        }
        
        if ($json->groupPermissions !== null) {
            foreach ($json->groupPermissions as $item) {
                $settings->setPermittedCustomerStatus(new IdType($item->id), new BoolType($item->isPermitted));
            }
        }
        
        return $settings;
    }
    
    
    /**
     * Serialize Addon Values
     *
     * Common method for serializing addon values in various resource serializer classes.
     *
     * @param \KeyValueCollection $addonValues
     *
     * @return array
     */
    protected function _serializeAddonValues(KeyValueCollection $addonValues)
    {
        if ($addonValues->count()) {
            $addonValuesArray = [];
            foreach ($addonValues->getArray() as $key => $value) {
                $addonValuesArray[$key] = $value;
                
                // add deprecated googleExportCondition
                if ($key === 'googleExportConditionId') {
                    switch ($value) {
                        case '3':
                            $googleExportCondition = 'erneuert';
                            break;
                        case '2':
                            $googleExportCondition = 'gebraucht';
                            break;
                        default:
                            $googleExportCondition = 'neu';
                    }
                    
                    $addonValuesArray['googleExportCondition'] = $googleExportCondition;
                }
            }
        } else {
            $addonValuesArray = null;
        }
        
        return $addonValuesArray;
    }
    
    
    /**
     * Deserialize Addon Values
     *
     * Common method for deserializing addon values in various resource serializer classes.
     *
     * @param $json
     *
     * @return array
     */
    protected function _deserializeAddonValues($json)
    {
        $itemAddonValuesArray = [];
        
        foreach ($json as $propertyKey => $propertyValue) {
            // map deprecated googleExportCondition to googleExportConditionId
            if ($propertyKey === 'googleExportCondition') {
                if (!isset($json->googleExportConditionId)) {
                    switch ($propertyValue) {
                        case 'erneuert':
                        case 'renewed':
                            $googleExportConditionId = '3';
                            break;
                        case 'gebraucht':
                        case 'used':
                            $googleExportConditionId = '2';
                            break;
                        default:
                            $googleExportConditionId = '1';
                    }
                    
                    $itemAddonValuesArray['googleExportConditionId'] = $googleExportConditionId;
                }
                
                continue;
            }
            
            $itemAddonValuesArray[$propertyKey] = $propertyValue;
        }
        
        return $itemAddonValuesArray;
    }
}