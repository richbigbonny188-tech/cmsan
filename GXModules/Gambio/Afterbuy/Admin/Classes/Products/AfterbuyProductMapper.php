<?php
/* --------------------------------------------------------------
   AfterbuyProductMapper.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products;

use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AdditionalDescriptionField;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AfterbuyProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\Attribut;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\BaseProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\BaseProductsRelationData;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\Discount;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\EBayVariationData;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\Feature;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\MultiLanguage;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ProductPicture;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ScaledDiscount;
use SimpleXMLElement;

/**
 * Class AfterbuyProductMapper
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products
 */
class AfterbuyProductMapper
{
    
    /**
     * Maps incoming XML (from GetShopProducts) to an AfterbuyProduct instance.
     *
     * @param SimpleXMLElement $xmlProduct
     *
     * @return AfterbuyProduct
     */
    public function createAfterbuyProductFromXml(SimpleXMLElement $xmlProduct): AfterbuyProduct
    {
        $abProduct = new AfterbuyProduct((string)$xmlProduct->EAN, (string)$xmlProduct->Name);
        $abProduct->setOriginalXml($xmlProduct->asXML());
        
        $abProduct->setProductID((int)$xmlProduct->ProductID);
        $abProduct->setAnr((float)$xmlProduct->Anr);
        $abProduct->setSeoName((string)$xmlProduct->SeoName);
        $abProduct->setModDate(static::convertDatetime((string)$xmlProduct->ModDate));
        $abProduct->setVariationName((string)$xmlProduct->VariationName);
        $abProduct->setBaseProductFlag((int)$xmlProduct->BaseProductFlag);
        if (!empty($xmlProduct->BaseProducts->BaseProduct)) {
            foreach ($xmlProduct->BaseProducts->BaseProduct as $baseProductXml) {
                $baseProduct = $this->createBaseProductFromXml($baseProductXml);
                $abProduct->addBaseProduct($baseProduct);
            }
        }
        $abProduct->setShortDescription((string)$xmlProduct->ShortDescription);
        $abProduct->setDescription((string)$xmlProduct->Description);
        $abProduct->setKeywords((string)$xmlProduct->Keywords);
        $abProduct->setAvailableShop((int)$xmlProduct->AvailableShop);
        $abProduct->setQuantity((int)$xmlProduct->Quantity);
        $abProduct->setStock((bool)(string)$xmlProduct->Stock);
        $abProduct->setDiscontinued((bool)(string)$xmlProduct->Discontinued);
        $abProduct->setMergeStock((bool)(string)$xmlProduct->MergeStock);
        $abProduct->setUnitOfQuantity((string)$xmlProduct->UnitOfQuantity);
        $abProduct->setBasePriceFactor(static::convertFloat((string)($xmlProduct->BasepriceFactor ?? '1')));
        //$abProduct->setMinimumStock((int)$xmlProduct->MinimumStock);
        $abProduct->setMinimumOrderQuantity((int)$xmlProduct->MinimumOrderQuantity);
        $abProduct->setSellingPrice(static::convertFloat((string)($xmlProduct->SellingPrice ?? '0.0')));
        $abProduct->setDealerPrice(static::convertFloat((string)($xmlProduct->DealerPrice ?? '0.0')));
        $abProduct->setLevel((int)($xmlProduct->Level ?? 0));
        $abProduct->setPosition((int)($xmlProduct->Position ?? 0));
        if (!empty($xmlProduct->ScaledDiscounts->ScaledDiscount)) {
            foreach ($xmlProduct->ScaledDiscounts->ScaledDiscount as $xmlScaledDiscount) {
                $scaledDiscount = $this->createScaledDiscountFromXml($xmlScaledDiscount);
                $abProduct->addScaledDiscount($scaledDiscount);
            }
        }
        $abProduct->setTaxRate(static::convertFloat((string)($xmlProduct->TaxRate ?? '0')));
        $abProduct->setWeight(static::convertFloat((string)($xmlProduct->Weight ?? '0')));
        $abProduct->setShippingGroup((string)$xmlProduct->ShippingGroup);
        $abProduct->setShopShippingGroup((string)$xmlProduct->ShopShippingGroup);
        //foreach ($xmlProduct->Features->Feature as $xmlFeature) {
        //    $feature = $this->createFeatureFromXml($xmlFeature);
        //    $abProduct->addFeature($feature);
        //}
        $abProduct->setFreeValue1((string)$xmlProduct->FreeValue1);
        $abProduct->setFreeValue2((string)$xmlProduct->FreeValue2);
        $abProduct->setFreeValue3((string)$xmlProduct->FreeValue3);
        $abProduct->setFreeValue4((string)$xmlProduct->FreeValue4);
        $abProduct->setFreeValue5((string)$xmlProduct->FreeValue5);
        $abProduct->setFreeValue6((string)$xmlProduct->FreeValue6);
        $abProduct->setFreeValue7((string)$xmlProduct->FreeValue7);
        $abProduct->setFreeValue8((string)$xmlProduct->FreeValue8);
        $abProduct->setFreeValue9((string)$xmlProduct->FreeValue9);
        $abProduct->setFreeValue10((string)$xmlProduct->FreeValue10);
        $abProduct->setDeliveryTime((string)$xmlProduct->DeliveryTime);
        $abProduct->setImageSmallURL((string)$xmlProduct->ImageSmallURL);
        $abProduct->setImageLargeURL((string)$xmlProduct->ImageLargeURL);
        $abProduct->setManufacturerStandardProductIDType((string)$xmlProduct->ManufacturerStandardProductIDType);
        $abProduct->setManufacturerStandardProductIDValue((string)$xmlProduct->ManufacturerStandardProductIDValue);
        $abProduct->setProductBrand((string)$xmlProduct->ProductBrand);
        $abProduct->setCustomsTariffNumber((string)$xmlProduct->CustomsTariffNumber);
        $abProduct->setGoogleProductCategory((string)$xmlProduct->GoogleProductCategory);
        $abProduct->setCountryOfOrigin((string)$xmlProduct->CountryOfOrigin);
        $abProduct->setManufacturerPartNumber((string)$xmlProduct->ManufacturerPartNumber);
        $abProduct->setCondition((int)$xmlProduct->Condition);
        $abProduct->setAgeGroup((int)$xmlProduct->AgeGroup);
        $abProduct->setGender((int)$xmlProduct->Gender);
        if (!empty($xmlProduct->MultiLanguage)) {
            foreach ($xmlProduct->MultiLanguage->children() as $languageCode => $xmlMultiLanguage) {
                $multiLanguage = $this->createMultiLanguageFromXml($xmlMultiLanguage);
                $abProduct->addMultiLanguage($languageCode, $multiLanguage);
            }
        }
        if (!empty($xmlProduct->ProductPictures->ProductPicture)) {
            foreach ($xmlProduct->ProductPictures->ProductPicture as $xmlProductPicture) {
                $productPicture = $this->createProductPictureFromXml($xmlProductPicture);
                $abProduct->addProductPicture($productPicture);
            }
        }
        if (!empty($xmlProduct->Catalogs->CatalogID)) {
            foreach ($xmlProduct->Catalogs->CatalogID as $xmlCatalogID) {
                $abProduct->addCatalogID((int)$xmlCatalogID);
            }
        }
        if (!empty($xmlProduct->Attributes->Attribut)) {
            foreach ($xmlProduct->Attributes->Attribut as $xmlAttribut) {
                $attribut = $this->createAttributFromXml($xmlAttribut);
                $abProduct->addAttribut($attribut);
            }
        }
        if (!empty($xmlProduct->Features->Feature)) {
            foreach ($xmlProduct->Features->Feature as $xmlFeature) {
                $feature = $this->createFeatureFromXml($xmlFeature);
                $abProduct->addFeature($feature);
            }
        }
        if (!empty($xmlProduct->Discounts->Discount)) {
            foreach ($xmlProduct->Discounts->Discount as $xmlDiscount) {
                $discount = $this->createDiscountFromXml($xmlDiscount);
                $abProduct->addDiscount($discount);
            }
        }
        if (!empty($xmlProduct->AdditionalDescriptionFields->AdditionalDescriptionField)) {
            foreach ($xmlProduct->AdditionalDescriptionFields->AdditionalDescriptionField as $xmlAdditionalDescriptionField) {
                $additionalDescriptionField = $this->createAdditionalDescriptionFieldFromXml($xmlAdditionalDescriptionField);
                $abProduct->addAdditionalDescriptionField($additionalDescriptionField);
            }
        }
        $abProduct->setProductShopOption((int)$xmlProduct->ProductShopOption);
        
        return $abProduct;
    }
    
    
    /**
     * @param SimpleXMLElement $xmlAdditionalDescriptionField
     *
     * @return AdditionalDescriptionField
     */
    protected function createAdditionalDescriptionFieldFromXml(SimpleXMLElement $xmlAdditionalDescriptionField) : AdditionalDescriptionField
    {
        return new AdditionalDescriptionField(
            (int)$xmlAdditionalDescriptionField->FieldID,
            (string)$xmlAdditionalDescriptionField->FieldName,
            (string)$xmlAdditionalDescriptionField->FieldLabel,
            (string)$xmlAdditionalDescriptionField->FieldContent
        );
    }
    
    
    /**
     * @param SimpleXMLElement $xmlScaledDiscount
     *
     * @return ScaledDiscount
     */protected function createScaledDiscountFromXml(SimpleXMLElement $xmlScaledDiscount): ScaledDiscount
    {
        $scaledDiscount = new ScaledDiscount((int)$xmlScaledDiscount->ScaledQuantity,
                                             static::convertFloat((string)$xmlScaledDiscount->ScaledPrice),
                                             static::convertFloat((string)$xmlScaledDiscount->ScaledDPrice));
        
        return $scaledDiscount;
    }
    
    
    /**
     * @param SimpleXMLElement $xmlBaseProduct
     *
     * @return BaseProduct
     */
    protected function createBaseProductFromXml(SimpleXMLElement $xmlBaseProduct): BaseProduct
    {
        $baseProduct = new BaseProduct((int)$xmlBaseProduct->BaseProductID);
        if (isset($xmlBaseProduct->BaseProductType)) {
            $baseProduct->setBaseProductType((int)$xmlBaseProduct->BaseProductType);
        }
        if (isset($xmlBaseProduct->BaseProductsRelationData)) {
            foreach ($xmlBaseProduct->BaseProductsRelationData as $xmlBaseProductsRelationDatum) {
                $baseProductsRelationData = new BaseProductsRelationData();
                if (isset($xmlBaseProductsRelationDatum->Quantity)) {
                    $baseProductsRelationData->setQuantity((int)$xmlBaseProductsRelationDatum->Quantity);
                }
                $baseProductsRelationData->setDefaultProduct((string)$xmlBaseProductsRelationDatum->DefaultProduct);
                $baseProductsRelationData->setPosition((string)$xmlBaseProductsRelationDatum->Position);
                $baseProductsRelationData->setVariationLabel((string)$xmlBaseProductsRelationDatum->VariationLabel);
                if (!empty($xmlBaseProductsRelationDatum->eBayVariationData)) {
                    foreach ($xmlBaseProductsRelationDatum->eBayVariationData as $xmlEBayVariationData) {
                        $eBayVariationData = new EBayVariationData((string)$xmlEBayVariationData->eBayVariationName,
                                                                   (string)$xmlEBayVariationData->eBayVariationValue,
                                                                   (int)$xmlEBayVariationData->eBayVariationPosition,
                                                                   (string)$xmlEBayVariationData->eBayVariationUrls,);
                        $baseProductsRelationData->addEBayVariationData($eBayVariationData);
                    }
                }
                $baseProduct->addBaseProductsRelationData($baseProductsRelationData);
            }
        }
        
        return $baseProduct;
    }
    
    
    /**
     * @param SimpleXMLElement $xmlDiscount
     *
     * @return Discount
     */
    protected function createDiscountFromXml(SimpleXMLElement $xmlDiscount): Discount
    {
        return new Discount((int)$xmlDiscount->ShopID,
                            (bool)(string)$xmlDiscount->DiscountActive,
                            (int)$xmlDiscount->ControlId,
                            (string)$xmlDiscount->PriceType,
                            (string)$xmlDiscount->NewPriceType,
                            static::convertDatetime((string)$xmlDiscount->StartDate),
                            static::convertDatetime((string)$xmlDiscount->ExpireDate),
                            (int)$xmlDiscount->Type,
                            static::convertFloat((string)$xmlDiscount->DiscountPercent),
                            static::convertFloat((string)$xmlDiscount->DiscountAmount),
                            static::convertFloat((string)$xmlDiscount->SavedAmount),
                            static::convertFloat((string)$xmlDiscount->DiscountedPrice),
                            isset($xmlDiscount->Quantity) ? (int)$xmlDiscount->Quantity : null);
    }
    
    
    /**
     * @param SimpleXMLElement $xmlAttribut
     *
     * @return Attribut
     */
    protected function createAttributFromXml(SimpleXMLElement $xmlAttribut): Attribut
    {
        return new Attribut((string)$xmlAttribut->AttributName,
                            (string)$xmlAttribut->AttributValue,
                            (int)$xmlAttribut->AttributType,
                            (int)$xmlAttribut->AttributRequired !== 0,
                            (int)$xmlAttribut->AttributPosition);
    }
    
    
    /**
     * @param SimpleXMLElement $xmlFeature
     *
     * @return Feature
     */
    protected function createFeatureFromXml(SimpleXMLElement $xmlFeature): Feature
    {
        return new Feature((string)$xmlFeature->Name, (string)$xmlFeature->Value, (int)$xmlFeature->ID);
    }
    
    
    /**
     * Creates a ProductPicture instance from XML
     *
     * @param SimpleXMLElement $xmlProductPicture
     *
     * @return ProductPicture
     */
    protected function createProductPictureFromXml(SimpleXMLElement $xmlProductPicture): ProductPicture
    {
        $productPicture = new ProductPicture();
        $productPicture->setNr((int)$xmlProductPicture->Nr);
        $productPicture->setUrl((string)$xmlProductPicture->Url);
        $productPicture->setAltText((string)$xmlProductPicture->AltText);
        if (!empty($xmlProductPicture->Childs->ProductPicture)) {
            foreach ($xmlProductPicture->Childs->ProductPicture as $xmlChild) {
                $childProductPicture = new ProductPicture();
                $childProductPicture->setTyp((int)$xmlChild->Typ);
                $childProductPicture->setUrl((string)$xmlChild->Url);
                $childProductPicture->setAltText((string)$xmlChild->AltText);
                $productPicture->addChild($childProductPicture);
            }
        }
        return $productPicture;
    }
    
    
    /**
     * Creates a MultiLanguage instance from XML
     *
     * @param SimpleXMLElement $xmlMultiLanguage
     *
     * @return MultiLanguage
     */
    protected function createMultiLanguageFromXml(SimpleXMLElement $xmlMultiLanguage): MultiLanguage
    {
        $multiLanguage = new MultiLanguage((string)$xmlMultiLanguage->Name);
        $multiLanguage->setAlias((string)$xmlMultiLanguage->Alias);
        $multiLanguage->setBeschreibung((string)$xmlMultiLanguage->Beschreibung);
        $multiLanguage->setKurzbeschreibung((string)$xmlMultiLanguage->Kurzbeschreibung);
        $multiLanguage->setVariationsname((string)$xmlMultiLanguage->Variationsname);
        $multiLanguage->setUst((string)$xmlMultiLanguage->ust);
        
        return $multiLanguage;
    }
    
    
    /**
     * Converts a timestamp in Afterbuy’s format into a DateTimeImmutable.
     *
     * Currently uses DateTime’s heuristics to parse the input, based on time zone Europe/Berlin.
     *
     * @param string $afterbuyDatetime
     *
     * @return \DateTimeImmutable
     */
    protected static function convertDatetime(string $afterbuyDatetime): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($afterbuyDatetime, new \DateTimeZone('Europe/Berlin'));
        } catch (\Exception $e) {
            return new \DateTimeImmutable();
        }
    }
    
    
    /**
     * Converts incoming „Floats“ from Afterbuy into real floats.
     *
     * Afterbuy uses the German comma-as-decimal-point notation.
     *
     * @param string $afterbuyFloat
     *
     * @return float
     */
    protected static function convertFloat(string $afterbuyFloat): float
    {
        return (float)str_replace(',', '.', $afterbuyFloat);
    }
}
