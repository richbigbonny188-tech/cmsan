<?php
/* --------------------------------------------------------------
   AfterbuyProductImporter.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products;

use BoolType;
use DecimalType;
use Doctrine\DBAL\Connection;
use EntityNotFoundException;
use ExistingFile;
use FilenameStringType;
use Gambio\Admin\Modules\ImageList\Model\Collections\Images;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\NewImage;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageListDoesNotExistException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\StorageOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService;
use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionValueDetails;
use Gambio\Admin\Modules\Option\Model\ValueObjects\NewOptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValuesProductDetails;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueStock;
use Gambio\Admin\Modules\Option\Services\Exceptions\CreationOfOptionsFailedException;
use Gambio\Admin\Modules\Option\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId as OptionOptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId as OptionImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock as ProductOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantDoesNotExist;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\CreationOfAdditionalFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\CreationOfAdditionalProductFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\DeletionOfAdditionalProductFieldsFailedException;
use GambioAfterbuyConfigurationStorage;
use GraduatedPrice;
use GraduatedPriceCollection;
use GXEngineQuantityUnit;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\Events\ProductPreparedForImport;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions\ImageRetrievalException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions\ProductImportException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AfterbuyProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\Attribut;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\BaseProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\BaseProductsRelationData;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\Discount;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ProductMapping;
use IdType;
use IntType;
use LanguageCode;
use LanguageHelper;
use LanguageHelperInterface;
use LegacyDependencyContainer;
use MainFactory;
use NonEmptyStringType;
use ProductGroupPrice;
use ProductImage;
use ProductImageContainer;
use ProductPrice;
use ProductPriceWriteService;
use ProductReadService;
use ProductWriteService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use QuantityUnitReadService;
use SpecialOffer;
use SpecialOfferDates;
use SpecialOfferId;
use SpecialOfferInterface;
use StaticGXCoreLoader;
use StringType;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsWriteService;

require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');


/**
 * Class AfterbuyProductImporter
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products
 */
class AfterbuyProductImporter implements LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    
    /**
     * @var ProductsMappingRepository
     */
    private ProductsMappingRepository $mappingRepository;
    
    
    /**
     * @var GambioAfterbuyConfigurationStorage
     */
    private GambioAfterbuyConfigurationStorage $moduleConfiguration;
    
    
    /**
     * @var ProductWriteService
     */
    private ProductWriteService $productWriteService;
    
    
    /**
     * @var ProductReadService
     */
    private ProductReadService $productReadService;
    
    
    /**
     * @var AdditionalOptionWriteService|mixed
     */
    private AdditionalOptionWriteService $productOptionWriteService;
    
    
    /**
     * @var ImageListFactory|mixed
     */
    private ImageListFactory $imageListFactory;
    
    
    /**
     * @var ImageListReadService|mixed
     */
    private ImageListReadService $imageListReadService;
    
    
    /**
     * @var ImageListWriteService|mixed
     */
    private ImageListWriteService $imageListWriteService;
    
    
    /**
     * @var LanguageHelperInterface|LanguageHelper|mixed
     */
    private LanguageHelperInterface $languageHelper;
    
    
    /**
     * @var OptionReadService|mixed
     */
    private OptionReadService $optionReadService;
    
    
    /**
     * @var OptionWriteService|mixed
     */
    private OptionWriteService $optionWriteService;
    
    
    /**
     * @var ProductVariantsWriteService|mixed
     */
    private ProductVariantsWriteService $productVariantsWriteService;
    
    
    /**
     * @var ProductVariantsReadService|mixed
     */
    private ProductVariantsReadService $productVariantsReadService;
    
    
    /**
     * @var ProductPriceWriteService
     */
    private ProductPriceWriteService $priceWriteService;
    
    
    /**
     * @var AdditionalFieldsHelper
     */
    private AdditionalFieldsHelper $additionalFieldsHelper;
    
    
    /**
     * @var bool
     */
    private bool $setOutOfStockProducts;
    
    
    /**
     * @var EventDispatcherInterface|mixed
     */
    private EventDispatcherInterface $eventDispatcher;
    
    
    /**
     * @param ProductsMappingRepository          $mappingRepository
     * @param GambioAfterbuyConfigurationStorage $moduleConfiguration
     * @param ProductWriteService                $productWriteService
     * @param ProductReadService                 $productReadService
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        ProductsMappingRepository          $mappingRepository,
        GambioAfterbuyConfigurationStorage $moduleConfiguration,
        ProductWriteService                $productWriteService,
        ProductReadService                 $productReadService
    ) {
        $this->mappingRepository   = $mappingRepository;
        $this->moduleConfiguration = $moduleConfiguration;
        $this->productWriteService = $productWriteService;
        $this->productReadService  = $productReadService;
        $this->logger              = new NullLogger();
        
        $this->optionReadService           = LegacyDependencyContainer::getInstance()->get(OptionReadService::class);
        $this->optionWriteService          = LegacyDependencyContainer::getInstance()->get(OptionWriteService::class);
        $this->productVariantsReadService  = LegacyDependencyContainer::getInstance()
            ->get(ProductVariantsReadService::class);
        $this->productVariantsWriteService = LegacyDependencyContainer::getInstance()
            ->get(ProductVariantsWriteService::class);
        $this->productOptionWriteService   = LegacyDependencyContainer::getInstance()
            ->get(AdditionalOptionWriteService::class);
        $this->priceWriteService           = \ProductPriceServiceFactory::writeService();
        
        $this->imageListFactory      = LegacyDependencyContainer::getInstance()->get(ImageListFactory::class);
        $this->imageListReadService  = LegacyDependencyContainer::getInstance()->get(ImageListReadService::class);
        $this->imageListWriteService = LegacyDependencyContainer::getInstance()->get(ImageListWriteService::class);
        $this->languageHelper        = MainFactory::create('LanguageHelper',
                                                           StaticGXCoreLoader::getDatabaseQueryBuilder());
        
        $this->additionalFieldsHelper = new AdditionalFieldsHelper();
        
        $this->setOutOfStockProducts = defined('GM_SET_OUT_OF_STOCK_PRODUCTS')
                                       && GM_SET_OUT_OF_STOCK_PRODUCTS === 'true';
        
        $this->eventDispatcher = LegacyDependencyContainer::getInstance()->get(EventDispatcherInterface::class);
    }
    
    
    /**
     * Imports a product from Afterbuy into the shop.
     *
     * Note: Only basic products and parents of variation sets are supported.
     *
     * @param AfterbuyProduct $afterbuyProduct
     * @param array           $categoryIds
     * @param int             $minimumInactiveLevel
     * @param int|null        $dealerGroupId
     *
     * @return int
     * @throws InsertionOfProductVariantsFailed
     * @throws ProductImportException
     * @throws StorageOfProductVariantsFailed
     * @throws EntityNotFoundException
     * @throws CreationOfAdditionalFieldFailedException
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws DeletionOfAdditionalProductFieldsFailedException
     */
    public function importProduct(
        AfterbuyProduct $afterbuyProduct,
        array           $categoryIds = [],
        int             $minimumInactiveLevel = 1000,
        ?int            $dealerGroupId = null
    ): int {
        if (empty($categoryIds)) {
            throw new ProductImportException('Import requires at least one category');
        }
        $this->logger->debug("Importing product {$afterbuyProduct->getProductID()} with BaseProductFlag {$afterbuyProduct->getBaseProductFlag()}");
        $mapping = $this->mappingRepository->findMappingByAfterbuyProduct($afterbuyProduct);
        if (!$mapping->isType(ProductMapping::MAPPING_NONE)) {
            $this->logger->info("Product already imported: {$afterbuyProduct->getProductID()} => {$mapping->getProductsId()}, updating");
            $product = $this->productReadService->getProductById(new IdType($mapping->getProductsId()));
        } else {
            $productSettings = MainFactory::create('ProductSettings');
            $product         = MainFactory::create('GXEngineProduct', $productSettings);
        }
        
        $defaultLanguageCode = new \LanguageCode(new StringType('de')); // default language for Afterbuy is always de!
        $syncType            = $this->moduleConfiguration->get('product_sync_type');
        
        $isActive = $afterbuyProduct->getLevel() < $minimumInactiveLevel;
        if ($isActive && $this->setOutOfStockProducts && $product instanceof \StoredProductInterface) {
            if ($product->getQuantity() <= 0 && $afterbuyProduct->getQuantity() > 0) {
                $isActive = true;
            }
            if ($product->getQuantity() > 0 && $afterbuyProduct->getQuantity() <= 0) {
                $isActive = false;
            }
        } else {
            $isActive = $isActive && $afterbuyProduct->getQuantity() > 0;
        }
        $product->setActive(new BoolType($isActive));
        $product->setMainCategoryId(new IdType($categoryIds[0]));
        $anr  = $afterbuyProduct->getAnr();
        $exan = $afterbuyProduct->getEAN();
        if ($anr !== null && $anr > 0 && $syncType === 'model_anr') {
            $product->setProductModel(new StringType((string)$anr));
        } elseif (!empty($exan) && $syncType === 'model_ean') {
            $product->setProductModel(new StringType($exan));
        } elseif ($syncType === 'model_prodid') {
            $product->setProductModel(new StringType((string)$afterbuyProduct->getProductID()));
        }
        
        $product->setName(new StringType($afterbuyProduct->getName()), $defaultLanguageCode);
        $product->setUrlKeywords(new StringType($afterbuyProduct->getSeoName()), $defaultLanguageCode);
        $description = $afterbuyProduct->getDescription();
        if (!static::isHtml($description)) {
            // htmlify the description
            $description = '<div class="ab_description">' . nl2br($description) . '</div>';
        }
        foreach ($afterbuyProduct->getAdditionalDescriptionFields() as $additionalDescriptionField) {
            $tabLabel    = htmlspecialchars($additionalDescriptionField->getFieldLabel());
            $description .= "[TAB:{$tabLabel}]";
            $tabContent  = $additionalDescriptionField->getFieldContent();
            if (!static::isHtml($tabContent)) {
                $tabContent = '<div class="ab_description_tab">' . nl2br($tabContent) . '</div>';
            }
            $description .= $tabContent;
        }
        if ($afterbuyProduct->getBaseProductFlag() === AfterbuyProduct::BASE_PRODUCT_FLAG_PRODUCT_SET_PARENT) {
            // main product of a product set
            $this->logger->debug("Making set description tab for {$afterbuyProduct->getProductID()}");
            $description .= '[TAB:Set]';
            $description .= '<div class="ab_description_tab ab_description_productset">';
            
            foreach ($afterbuyProduct->getBaseProducts() as $baseProduct) {
                $baseAfterbuyProduct = $afterbuyProduct->getProductSetProduct($baseProduct->getBaseProductID());
                if ($baseAfterbuyProduct === null) {
                    continue;
                }
                $description .= '<div class="ab_productset_product">';
                $description .= '<span class="ab_productset_qty">'
                                . (int)$baseProduct->getBaseProductsRelationData()[0]->getQuantity() . '</span>';
                $description .= ' <span class="ab_productset_multiply">x</span> ';
                $description .= '<span class="ab_productset_name">' . htmlspecialchars($baseAfterbuyProduct->getName())
                                . '</span>';
                $description .= '</div>';
            }
            $description .= '</div>';
        }
        $product->setDescription(new StringType($description), $defaultLanguageCode);
        $product->setShortDescription(new StringType($afterbuyProduct->getShortDescription()), $defaultLanguageCode);
        $product->setKeywords(new StringType($afterbuyProduct->getKeywords()), $defaultLanguageCode);
        $product->setSortOrder(new IntType((int)$afterbuyProduct->getPosition()));
        if ($afterbuyProduct->getProductShopOption() === 1) {
            // Startseite
            $product->getSettings()->setShowOnStartpage(new BoolType(true));
        } elseif ($afterbuyProduct->getProductShopOption() === 2) {
            // Top-Seller
            $product->getSettings()->setShowOnStartpage(new BoolType(true));
        } else {
            $product->getSettings()->setShowOnStartpage(new BoolType(false));
        }
        $product->setQuantity(new DecimalType($afterbuyProduct->getQuantity()));
        $unitOfQuantity = $afterbuyProduct->getUnitOfQuantity();
        if (!empty($unitOfQuantity)) {
            //$quantityUnitId = $this->findQuantityUnitId($unitOfQuantity);
            //$product->setQuantityUnitId($quantityUnitId);
            $vpeId = $this->findVpeId($unitOfQuantity);
            $product->setVpeId($vpeId);
            $product->setVpeActive(new BoolType(true));
        }
        $basepriceFactor = $afterbuyProduct->getBasepriceFactor();
        if ($basepriceFactor > 0 && $basepriceFactor !== 1.0) {
            $product->setVpeValue(new DecimalType($basepriceFactor));
            $product->setVpeActive(new BoolType(true));
        }
        
        $minimumOrderQuantity = new DecimalType(1.0);
        $product->setMinimumQuantity($minimumOrderQuantity);
        $product->getSettings()->setMinOrder($minimumOrderQuantity);
        
        $product->setAddonValue(new StringType('googleExportConditionId'),
                                new StringType((string)$afterbuyProduct->getCondition()));
        $product->setAddonValue(new StringType('brandName'),
                                new StringType($afterbuyProduct->getProductBrand()));
        
        // @todo: GoogleProductCategory … Afterbuy currently does not include this in GetShopProducts.
        
        $ageGroup           = (int)$afterbuyProduct->getAgeGroup();
        $ageGroupAddonValue = '';
        if ($ageGroup > 0) {
            $ageGroupAddonValue = $ageGroup === 1 ? 'Erwachsene' : 'Kinder';
        }
        $product->setAddonValue(new StringType('ageGroup'), new StringType($ageGroupAddonValue));
        $gender           = (int)$afterbuyProduct->getGender();
        $genderMap        = [
            1 => 'Unisex',
            2 => 'Herren',
            3 => 'Damen',
        ];
        $genderAddonValue = array_key_exists($gender, $genderMap) ? $genderMap[$gender] : '';
        $product->setAddonValue(new StringType('gender'), new StringType($genderAddonValue));
        if ($afterbuyProduct->getManufacturerStandardProductIDType() === 'GTIN'
            || $afterbuyProduct->getManufacturerStandardProductIDType() === 'EAN') {
            $product->setEan(new StringType($afterbuyProduct->getManufacturerStandardProductIDValue()));
        } elseif ($afterbuyProduct->getManufacturerStandardProductIDType() === 'ISBN') {
            $product->setAddonValue(new StringType('codeIsbn'),
                                    new StringType($afterbuyProduct->getManufacturerStandardProductIDValue()));
        }
        $mpn = $afterbuyProduct->getManufacturerPartNumber();
        if (!empty($mpn)) {
            $product->setAddonValue(new StringType('codeMpn'), new StringType($mpn));
        }
        
        if ($afterbuyProduct->getBaseProductFlag() === AfterbuyProduct::BASE_PRODUCT_FLAG_VARIATIONS_PARENT) {
            $price = 0.0;
        } else {
            $price = $afterbuyProduct->getSellingPrice();
            if ($afterbuyProduct->getTaxRate() > 0) {
                $price /= (1 + ($afterbuyProduct->getTaxRate() / 100));
            }
        }
        $product->setPrice(new DecimalType($price));
        
        $taxClassId = $this->findTaxClassId((string)$afterbuyProduct->getTaxRate());
        if ($taxClassId === null) {
            throw new \RuntimeException("unsupported tax rate {$afterbuyProduct->getTaxRate()}");
        }
        $product->setTaxClassId(new IdType($taxClassId));
        $product->setWeight(new DecimalType($afterbuyProduct->getWeight()));
        
        try {
            $product->setImageContainer($this->makeProductImageContainer($product, $afterbuyProduct));
        } catch (ImageRetrievalException $e) {
            $this->logger->notice("Product image unavailable.");
        }
        
        /** @var LanguageHelper $languageHelper */
        $languageHelper = MainFactory::create('LanguageHelper', StaticGXCoreLoader::getDatabaseQueryBuilder());
        /** @var LanguageCode $languageCode */
        foreach ($languageHelper->getLanguageCodes() as $languageCode) {
            $multiLanguage = $afterbuyProduct->getMultiLanguage($languageCode->asString());
            if ($multiLanguage === null && $languageCode->asString() === 'EN') {
                $multiLanguage = $afterbuyProduct->getMultiLanguage('gb');
                if ($multiLanguage === null) {
                    $multiLanguage = $afterbuyProduct->getMultiLanguage('us');
                }
            }
            if ($multiLanguage !== null) {
                $product->setName(new StringType((string)$multiLanguage->getName()), $languageCode);
                $product->setShortDescription(new StringType((string)$multiLanguage->getKurzbeschreibung()),
                                              $languageCode);
                $product->setDescription(new StringType((string)$multiLanguage->getBeschreibung()), $languageCode);
            }
        }
        
        $this->eventDispatcher->dispatch(ProductPreparedForImport::create($product, $afterbuyProduct));
        
        if ($product instanceof \StoredProductInterface) {
            $this->productWriteService->updateProduct($product);
            $productId = $product->getProductId();
            //$this->productWriteService->deleteProductLinks(new IdType($productId));
            foreach ($categoryIds as $categoryId) {
                $this->productWriteService->linkProduct(new IdType($productId),
                                                        new IdType($categoryId));
            }
            // handle variants
            if ($afterbuyProduct->getBaseProductFlag() === AfterbuyProduct::BASE_PRODUCT_FLAG_VARIATIONS_PARENT) {
                // Current product is parent for variations
                $this->updateVariations($productId, $afterbuyProduct);
            }
            if (count($afterbuyProduct->getAttributes()) > 0) {
                $this->logger->info("Product {$afterbuyProduct->getProductID()}/$productId has attributes, removing and re-adding options");
                $this->productOptionWriteService->deleteAllProductOptionsByProductId($productId);
                $this->makeProductOptions($productId, $afterbuyProduct);
            }
        } else {
            $productId = $this->productWriteService->createProduct($product);
            // N. B.: ProductWriteService::createProduct() will implicitly link the new product to category 0 (TOP)
            $this->productWriteService->changeProductLink(new IdType($productId),
                                                          new IdType(0),
                                                          new IdType($categoryIds[0]));
            foreach ($categoryIds as $categoryId) {
                $this->productWriteService->linkProduct(new IdType($productId),
                                                        new IdType($categoryId));
            }
            $productMapping = new ProductMapping($productId, null, $afterbuyProduct->getProductID());
            $this->mappingRepository->addMapping($productMapping, 'afterbuy');
            
            // handle variants
            if ($afterbuyProduct->getBaseProductFlag() === AfterbuyProduct::BASE_PRODUCT_FLAG_VARIATIONS_PARENT) {
                // Current product is parent for variations
                $this->makeVariations($productId, $afterbuyProduct);
            }
            
            if (count($afterbuyProduct->getAttributes()) > 0) {
                $this->logger->info("Product {$afterbuyProduct->getProductID()}/$productId has attributes, adding options");
                $this->makeProductOptions($productId, $afterbuyProduct);
            }
        }
        set_shipping_status($productId, null, $afterbuyProduct->getQuantity());
        
        $this->importGraduatedAndGroupPrices($productId,
                                             $afterbuyProduct,
                                             $product->getPrice(),
                                             $taxClassId,
                                             $dealerGroupId);
        
        $specialOffer   = $this->findSpecialOfferByProductId($productId);
        $specialOfferId = $specialOffer === null ? null : $specialOffer->id();
        foreach ($afterbuyProduct->getDiscounts() as $discount) {
            if ($this->addSpecialOfferByDiscount($productId, $discount, $afterbuyProduct, $specialOfferId)) {
                break;
            }
        }
        
        $features = $afterbuyProduct->getFeatures();
        $this->additionalFieldsHelper->wipeAdditionalProductFields($productId);
        if (!empty($features)) {
            $languageCode = 'de';
            foreach ($features as $feature) {
                $this->logger->debug("Setting feature {$feature->getName()} to {$feature->getValue()} for $productId/{$languageCode}");
                $this->additionalFieldsHelper->setAdditionalFieldValue($productId,
                                                                       $feature->getName(),
                                                                       $feature->getValue());
            }
        }
        
        return $productId;
    }
    
    
    /**
     * @param string $text
     *
     * @return bool
     */
    private static function isHtml(string $text): bool
    {
        return stripos($text, '<br') !== false
               || stripos($text, '<div') !== false
               || stripos($text, '<p') !== false;
    }
    
    
    /**
     * @param int $productId
     *
     * @return SpecialOffer|null
     */
    private function findSpecialOfferByProductId(int $productId): ?SpecialOffer
    {
        $specialOffersReadService = \SpecialOffersServiceFactory::readService();
        $searchCondition          = \SpecialOfferSearchCondition::createByArray([
                                                                                    'match' => [
                                                                                        'specials.products_id' => $productId,
                                                                                    ],
                                                                                ]);
        try {
            $offers = $specialOffersReadService->getBy($searchCondition);
            // there can only be one
            /** @var SpecialOfferInterface $offer */
            $offer = $offers->getArray()[0];
            $this->logger->debug("Product $productId has special offer {$offer->id()}");
            
            return $offer;
        } catch (\SpecialOfferCollectionNotFoundException $e) {
            $this->logger->debug("Product $productId has no special offers.");
        }
        
        return null;
    }
    
    
    /**
     * @param int $productId
     *
     * @return void
     */
    private function removeSpecialOffers(int $productId): void
    {
        $offer = $this->findSpecialOfferByProductId($productId);
        if ($offer === null) {
            return;
        }
        $specialOffersWriteService = \SpecialOffersServiceFactory::writeService();
        $this->logger->debug("Deleting special offer {$offer->id()} for product $productId");
        $specialOffersWriteService->delete($offer);
    }
    
    
    /**
     * @param int             $productId
     * @param Discount        $discount
     * @param AfterbuyProduct $afterbuyProduct
     * @param int|null        $specialOfferId
     *
     * @return bool
     */
    private function addSpecialOfferByDiscount(
        int             $productId,
        Discount        $discount,
        AfterbuyProduct $afterbuyProduct,
        ?int            $specialOfferId = null
    ): bool {
        $specialOffersWriteService = \SpecialOffersServiceFactory::writeService();
        if ($discount->getExpireDate()->getTimestamp() < time()) {
            $this->logger->debug("Skipping discount expired on {$discount->getExpireDate()->format('c')} on product $productId");
            
            return false;
        }
        $price    = $discount->getDiscountedPrice() / (1 + ($afterbuyProduct->getTaxRate() / 100));
        $dtNow    = new \DateTime();
        $isActive = $discount->isDiscountActive() && $discount->getStartDate()->getTimestamp() < time();
        $quantity = $afterbuyProduct->getQuantity();
        if ($discount->getControlId() === 2) {
            // ControlID === 2 => Live Shopping => use discount’s quantity
            $quantity = $discount->getQuantity() ?? $afterbuyProduct->getQuantity();
        }
        $specialOfferInfo  = \SpecialOfferInformation::create((float)$quantity,
                                                              $price,
                                                              $isActive,
                                                              $productId);
        $specialOfferDates = SpecialOfferDates::create(\DateTime::createFromImmutable($discount->getExpireDate()),
                                                       $dtNow,
                                                       $dtNow,
                                                       \DateTime::createFromImmutable($discount->getStartDate()));
        $specialOffersWriteService->save(SpecialOffer::create(SpecialOfferId::create($specialOfferId),
                                                              $specialOfferInfo,
                                                              $specialOfferDates));
        
        return true;
    }
    
    
    /**
     * @param int             $productId
     * @param AfterbuyProduct $afterbuyProduct
     * @param float           $productPrice
     * @param int             $taxClassId
     * @param int|null        $dealerGroupId
     *
     * @return void
     * @throws EntityNotFoundException
     */
    private function importGraduatedAndGroupPrices(
        int             $productId,
        AfterbuyProduct $afterbuyProduct,
        float           $productPrice,
        int             $taxClassId,
        ?int            $dealerGroupId = null
    ): void {
        // group prices, dealer prices, scaled discounts
        $defaultCustomerGroupId = (int)DEFAULT_CUSTOMERS_STATUS_ID;
        $guestCustomerGroupId   = (int)DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
        
        $scaledDiscounts         = $afterbuyProduct->getScaledDiscounts();
        $guestGraduatedPrices    = [];
        $customerGraduatedPrices = [];
        $dealerGraduatedPrices   = [];
        foreach ($scaledDiscounts as $scaledDiscount) {
            if ($scaledDiscount->getScaledQuantity() === 0 || $scaledDiscount->getScaledQuantity() === 1) {
                continue;
            }
            $scaledGuestPrice          = $scaledDiscount->getScaledPrice() / (1 + ($afterbuyProduct->getTaxRate()
                                                                                   / 100));
            $scaledCustomerPrice       = $scaledDiscount->getScaledPrice() / (1 + ($afterbuyProduct->getTaxRate()
                                                                                   / 100));
            $guestGraduatedPrices[]    = GraduatedPrice::createWithThreshold($scaledGuestPrice,
                                                                             $scaledDiscount->getScaledQuantity());
            $customerGraduatedPrices[] = GraduatedPrice::createWithThreshold($scaledCustomerPrice,
                                                                             $scaledDiscount->getScaledQuantity());
            if ($dealerGroupId !== null) {
                $dealerGraduatedPrices[] = GraduatedPrice::createWithThreshold($scaledDiscount->getScaledDPrice(),
                                                                               $scaledDiscount->getScaledQuantity());
            }
        }
        
        $groupPricesData = [
            $groupPricesData[] = ProductGroupPrice::create($guestCustomerGroupId,
                                                           $productPrice,
                                                           GraduatedPriceCollection::collect($guestGraduatedPrices)),
            $groupPricesData[] = ProductGroupPrice::create($defaultCustomerGroupId,
                                                           $productPrice,
                                                           GraduatedPriceCollection::collect($customerGraduatedPrices)),
        ];
        if ($dealerGroupId !== null) {
            $groupPricesData[] = ProductGroupPrice::create($dealerGroupId,
                                                           $afterbuyProduct->getDealerPrice(),
                                                           GraduatedPriceCollection::collect($dealerGraduatedPrices));
        }
        $groupPrices = \ProductGroupPriceCollection::collect($groupPricesData);
        $this->priceWriteService->save(ProductPrice::create($productId, $productPrice, $taxClassId, $groupPrices),
                                       new IdType($productId));
    }
    
    
    /**
     * Converts product images from Afterbuy to Gambio.
     *
     * Uses ImageLargeURL as primary, ProductPictures as additional images. Image files are retrieved and stored
     * locally.
     *
     * @param \GXEngineProduct $product
     * @param AfterbuyProduct  $afterbuyProduct
     *
     * @return ProductImageContainer
     * @throws ImageRetrievalException
     */
    private function makeProductImageContainer(
        \GXEngineProduct $product,
        AfterbuyProduct  $afterbuyProduct
    ): ProductImageContainer {
        $defaultLanguageCode     = new \LanguageCode(new StringType('de')); // default language for Afterbuy is always de!
        $primaryImageUrl         = $afterbuyProduct->getImageLargeURL();
        $primaryImageUrlBasename = basename($primaryImageUrl);
        $this->logger->debug("Primary Image: {$primaryImageUrl} => $primaryImageUrlBasename");
        $imageContainer       = $product->getImageContainer();
        $primaryImageFileName = $imageContainer->getPrimary()->getFilename();
        $this->logger->debug("Primary image file name: '{$primaryImageFileName}'");
        if (empty($primaryImageUrl)) {
            if (!empty($primaryImageFileName)) {
                // image was deleted at Afterbuy
                $this->logger->debug("deleting primary image");
                $primaryImageFilePath = new \RelativeFilePathStringType($primaryImageFileName);
                $this->productWriteService->deleteProductImage($primaryImageFilePath);
                $imageContainer->delete($primaryImageFilePath);
            }
        } elseif ($primaryImageUrlBasename !== $primaryImageFileName) {
            // image referenced by ImageLargeURL differs (by name) from current primary image
            $primaryImageFile = $this->importProductImageFromUrl($primaryImageUrl);
            $this->logger->debug("Retrieved $primaryImageUrlBasename as $primaryImageFile");
            if (!$this->imageFilesAreIdentical($primaryImageFileName, $primaryImageFile)) {
                $productImage = new ProductImage(new FilenameStringType($primaryImageFile));
                $imageContainer->setPrimary($productImage);
            } else {
                $this->logger->debug("Image files $primaryImageFileName and $primaryImageFile are identical, deleting $primaryImageFile");
                $this->deleteImageFile($primaryImageFile);
            }
            
            $productPictures = $afterbuyProduct->getProductPictures();
            foreach ($productPictures as $productPicture) {
                $additionalImageFileName = $this->importProductImageFromUrl($productPicture->getUrl());
                
                $additionalImageExists = false;
                /** @var \ProductImageInterface $existingAdditionalImage */
                foreach ($imageContainer->getAdditionals() as $existingAdditionalImage) {
                    if ($this->imageFilesAreIdentical($additionalImageFileName,
                                                      $existingAdditionalImage->getFilename())) {
                        $this->logger->debug("Image file $additionalImageFileName found among additional images");
                        $additionalImageExists = true;
                        break;
                    }
                }
                
                if (!$additionalImageExists) {
                    $this->logger->debug("Image file $additionalImageFileName not found among additional images, adding");
                    $additionalProductImage = new ProductImage(new FilenameStringType($additionalImageFileName));
                    $additionalProductImage->setAltText(new StringType($productPicture->getAltText()),
                                                        $defaultLanguageCode);
                    $imageContainer->addAdditional($additionalProductImage);
                } else {
                    $this->logger->debug("deleting temporary additional image file $additionalImageFileName");
                    $this->deleteImageFile($additionalImageFileName);
                }
            }
        } else {
            $this->logger->debug("TO CHECK:\n" . print_r($primaryImageUrlBasename, true) . "\n"
                                 . print_r($primaryImageFileName, true));
        }
        
        return $imageContainer;
    }
    
    
    /**
     * Finds or creates an option (and its values) by name (German).
     *
     * Note: The $optionValueNames aren’t strictly necessary here but should be provided for the sake of efficiency.
     *
     * @param string $optionName
     * @param array  $optionValueNames
     *
     * @return int
     * @throws OperationHasNotBeenPermittedException
     * @throws CreationOfOptionsFailedException
     */
    public function findOrMakeOption(string $optionName, array $optionValueNames): int
    {
        $primaryLanguageCode = 'de';
        $languageCodes       = ['de', 'en'];
        $allOptions          = $this->optionReadService->getAllOptions();
        foreach ($allOptions as $option) {
            if ($option->label($primaryLanguageCode) === $optionName) {
                return $option->id();
            }
        }
        
        // not found, must create
        $newOptionValuesArray = [];
        $valueSortOrder       = 0;
        foreach ($optionValueNames as $optionValueName) {
            $optionValueDetailsArray = [];
            foreach ($languageCodes as $languageCode) {
                $optionValueDetailsArray[] = OptionValueDetail::create($languageCode,
                                                                       $optionValueName,
                                                                       $optionValueName);
            }
            $newOptionValuesArray[] = NewOptionValue::create(OptionValueDetails::create(...$optionValueDetailsArray),
                                                             OptionValuesProductDetails::create('',
                                                                                                0.0,
                                                                                                0.0),
                                                             OptionValueStock::create(OptionValueStock::NOT_MANAGED_STOCK_TYPE,
                                                                                      0,
                                                                                      false),
                                                             $valueSortOrder++,
                                                             '');
        }
        $newOptionValues = NewOptionValues::create(...$newOptionValuesArray);
        
        $optionDetailsArray = [];
        foreach ($languageCodes as $languageCode) {
            $optionDetailsArray[] = OptionDetail::create($languageCode, $optionName, $optionName, $optionName);
        }
        $optionDetails = OptionDetails::create(...$optionDetailsArray);
        $sortOrder     = 0;
        $optionId      = $this->optionWriteService->createOption($optionDetails,
                                                                 $newOptionValues,
                                                                 OptionType::create(OptionType::DROPDOWN_TYPE),
                                                                 $sortOrder);
        $this->logger->debug("created option {$optionId->value()} ({$optionName})");
        
        return $optionId->value();
    }
    
    
    /**
     * For an option value with a given name (German) finds or creates the required data structures (db entries) and
     * returns an OptionValue id.
     *
     * @param int    $optionId
     * @param string $optionValueName
     *
     * @return int
     */
    public function findOrMakeOptionValue(int $optionId, string $optionValueName): int
    {
        $primaryLanguageCode = 'de';
        $languageCodes       = ['de', 'en'];
        try {
            $option = $this->optionReadService->getOptionById($optionId);
            foreach ($option->values() as $value) {
                if ($value->label($primaryLanguageCode) === $optionValueName) {
                    return $value->id();
                }
            }
            if (empty($optionValueName)) {
                $optionValueName = "Unnamed value";
            }
            // not found, must create
            $optionValueDetailsArray = [];
            foreach ($languageCodes as $languageCode) {
                $optionValueDetailsArray[] = OptionValueDetail::create($languageCode,
                                                                       $optionValueName,
                                                                       $optionValueName);
            }
            $valueSortOrder = 0;
            $option->addNewValues(NewOptionValue::create(OptionValueDetails::create(...$optionValueDetailsArray),
                                                         OptionValuesProductDetails::create('',
                                                                                            0.0,
                                                                                            0.0),
                                                         OptionValueStock::create(OptionValueStock::NOT_MANAGED_STOCK_TYPE,
                                                                                  0,
                                                                                  false),
                                                         $valueSortOrder,
                                                         ''));
            $this->optionWriteService->storeOptions($option);
            
            $option = $this->optionReadService->getOptionById($optionId);
            foreach ($option->values() as $value) {
                if ($value->label($primaryLanguageCode) === $optionValueName) {
                    return $value->id();
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), debug_backtrace());
        }
        throw new \RuntimeException('could not find ID of newly created OptionValue');
    }
    
    
    /**
     * @param int             $productId
     * @param AfterbuyProduct $afterbuyProduct
     *
     * @return void
     * @throws CreationOfOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function makeProductOptions(int $productId, AfterbuyProduct $afterbuyProduct): void
    {
        $this->logger->debug("Making product options for {$afterbuyProduct->getProductID()}/{$productId}");
        $modelNumber = ''; // $afterbuyProduct->getEAN(); ?
        $weight      = 0.0; // $afterbuyProduct->getWeight(); ?
        $price       = 0.0;
        /** @var Attribut $attribute */
        foreach ($afterbuyProduct->getAttributes() as $attribut) {
            if ($attribut->getType() !== 2) {
                $this->logger->debug("Attribut with type {$attribut->getType()} not supported, skipping");
                continue; // only dropdown selections are supported
            }
            $optionValueNames = explode(';', $attribut->getValue());
            $optionId         = $this->findOrMakeOption($attribut->getName(), $optionValueNames);
            foreach ($optionValueNames as $optionValueName) {
                $optionValueId = $this->findOrMakeOptionValue($optionId, $optionValueName);
                $this->logger->debug("Adding option {$attribut->getName()} ($optionId) with value $optionValueName ($optionValueId) to product $productId");
                $this->productOptionWriteService->createProductOption($productId,
                                                                      OptionOptionAndOptionValueId::create($optionId,
                                                                                                           $optionValueId),
                                                                      OptionImageListId::createAsNonExistent(),
                                                                      OptionValueCustomization::create($modelNumber,
                                                                                                       $weight,
                                                                                                       $price),
                                                                      ProductOptionStock::create(),
                                                                      $attribut->getPosition());
            }
        }
    }
    
    
    /**
     * For Afterbuy products w/ BaseProductFlag == 1 this creates Options/Property Combinations to represent the
     * variations.
     *
     * @param int             $productId
     * @param AfterbuyProduct $afterbuyProduct
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws CreationOfOptionsFailedException
     * @throws ImageListDoesNotExistException
     * @throws ImageRetrievalException
     * @throws InsertionOfProductVariantsFailed
     * @throws NotFoundExceptionInterface
     * @throws OperationHasNotBeenPermittedException
     * @throws StorageOfImageListsFailedException
     */
    public function makeVariations(int $productId, AfterbuyProduct $afterbuyProduct): void
    {
        if ($afterbuyProduct->getBaseProductFlag() !== AfterbuyProduct::BASE_PRODUCT_FLAG_VARIATIONS_PARENT) {
            throw new \RuntimeException('Variations can only be made from an Afterbuy product w/ BaseProductFlag == 1');
        }
        
        $discountFactor   = $this->getDiscountFactor($afterbuyProduct);
        $variationSummary = static::makeVariationSummary($afterbuyProduct);
        $this->logger->debug("Variation Summary:\n" . print_r($variationSummary, true));
        $optionIdCache      = [];
        $optionValueIdCache = [];
        foreach ($variationSummary as $optionName => $optionValueNames) {
            $optionId = $this->findOrMakeOption($optionName, $optionValueNames);
            $this->logger->debug("Option {$optionName} found/made with ID {$optionId}");
            $optionIdCache[$optionName]    = $optionId;
            $optionValueIdCache[$optionId] = [];
            foreach ($optionValueNames as $optionValueName) {
                $optionValueId                                   = $this->findOrMakeOptionValue($optionId,
                                                                                                $optionValueName);
                $optionValueIdCache[$optionId][$optionValueName] = $optionValueId;
                $this->logger->debug("OptionValue {$optionValueName} found/made with ID {$optionValueId}");
            }
        }
        
        /** @var BaseProduct $baseProduct */
        foreach ($afterbuyProduct->getBaseProducts() as $baseProduct) {
            $variantProduct = $afterbuyProduct->getVariantProduct($baseProduct->getBaseProductID());
            if ($variantProduct === null) {
                continue; // variant product not fetched?!
            }
            $this->logger->debug("Creating variant for BaseProduct {$baseProduct->getBaseProductID()}");
            /** @var BaseProductsRelationData $relationData */
            $relationData                 = $baseProduct->getBaseProductsRelationData()[0];
            $optionAndOptionValueIdsArray = [];
            foreach ($relationData->getEBayVariationData() as $EBayVariationDatum) {
                $optionName                     = $EBayVariationDatum->getEBayVariationName();
                $optionId                       = $optionIdCache[$optionName];
                $optionValueName                = $EBayVariationDatum->getEBayVariationValue();
                $optionValueId                  = $optionValueIdCache[$optionId][$optionValueName];
                $optionAndOptionValueId         = OptionAndOptionValueId::create($optionId, $optionValueId);
                $optionAndOptionValueIdsArray[] = $optionAndOptionValueId;
                $this->logger->debug("Option {$optionName} ({$optionId}), Value {$optionValueName} ({$optionValueId})");
            }
            
            $optionAndOptionValueIds = OptionAndOptionValueIds::create(...$optionAndOptionValueIdsArray);
            
            $imageList   = $this->makeImageListForVariantProduct($variantProduct);
            $imageListId = $imageList->id();
            
            $vpeUnitId            = $this->findVpeId($variantProduct->getUnitOfQuantity());
            $vpeUnitIdInt         = $vpeUnitId === null ? null : $vpeUnitId->asInt();
            $deliveryTimeId       = $this->findDeliveryTimeId();
            $price                = $variantProduct->getSellingPrice() / (1 + ($variantProduct->getTaxRate() / 100));
            $price                *= $discountFactor;
            $productCustomization = ProductCustomization::create(ProductCustomization::PRICE_TYPE_REPLACING,
                                                                 $price,
                                                                 ProductCustomization::WEIGHT_TYPE_REPLACING,
                                                                 $variantProduct->getWeight(),
                                                                 $variantProduct->getBasepriceFactor(),
                                                                 $deliveryTimeId,
                                                                 $vpeUnitIdInt);
            
            $ean  = $variantProduct->getManufacturerStandardProductIDType()
                    === 'EAN' ? $variantProduct->getManufacturerStandardProductIDValue() : '';
            $gtin = $variantProduct->getManufacturerStandardProductIDType()
                    === 'GTIN' ? $variantProduct->getManufacturerStandardProductIDValue() : '';
            $asin = $variantProduct->getManufacturerStandardProductIDType()
                    === 'ASIN' ? $variantProduct->getManufacturerStandardProductIDValue() : '';
            $ean  = (empty($ean) && !empty($gtin)) ? $gtin : $ean;
            $gtin = (empty($gtin) && !empty($ean)) ? $ean : $gtin;
            
            $productIdentificationNumbers = ProductIdentificationNumbers::create($variantProduct->getEAN(),
                                                                                 $gtin,
                                                                                 $gtin,
                                                                                 $asin);
            $productVariantStock          = ProductVariantStock::create($variantProduct->getQuantity());
            $productVariantId             = $this->productVariantsWriteService->createProductVariant($productId,
                                                                                                     $optionAndOptionValueIds,
                                                                                                     $imageListId,
                                                                                                     $productCustomization,
                                                                                                     $productIdentificationNumbers,
                                                                                                     $productVariantStock);
            $mapping                      = new ProductMapping($productId,
                                                               $productVariantId->value(),
                                                               $variantProduct->getProductID());
            $this->logger->info("Created variant {$productId}x{$productVariantId->value()} for {$variantProduct->getProductID()}");
            $this->mappingRepository->addMapping($mapping, 'afterbuy');
        }
    }
    
    
    /**
     * Updates variations of a product.
     *
     * @param int             $productId
     * @param AfterbuyProduct $afterbuyProduct
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws ImageListDoesNotExistException
     * @throws ImageRetrievalException
     * @throws NotFoundExceptionInterface
     * @throws StorageOfImageListsFailedException
     * @throws StorageOfProductVariantsFailed
     */
    private function updateVariations(int $productId, AfterbuyProduct $afterbuyProduct): void
    {
        $this->logger->info("Updating variations for product $productId");
        $discountFactor = $this->getDiscountFactor($afterbuyProduct);
        /** @var BaseProduct $baseProduct */
        foreach ($afterbuyProduct->getBaseProducts() as $baseProduct) {
            $variantProduct = $afterbuyProduct->getVariantProduct($baseProduct->getBaseProductID());
            if ($variantProduct === null) {
                continue; // variant product not fetched?!
            }
            $mapping = $this->mappingRepository->findMappingByAfterbuyProduct($variantProduct);
            if ($mapping->getType() !== ProductMapping::MAPPING_COMBI) {
                $this->logger->error("Cannot use Afterbuy product {$afterbuyProduct->getProductID()} to update variations: mapping incompatible!");
                
                return;
            }
            try {
                $variant = $this->productVariantsReadService->getProductVariantById($mapping->getCombiId());
            } catch (ProductVariantDoesNotExist $e) {
                $this->logger->error("Unhandled: Mapping for Afterbuy product {$afterbuyProduct->getProductID()} points to combi {$mapping->getCombiId()} which does not exist.");
                
                return;
            }
            $this->logger->debug("Updating {$productId}x{$mapping->getCombiId()} based on {$variantProduct->getProductID()}");
            $vpeUnitId            = $this->findVpeId($variantProduct->getUnitOfQuantity());
            $vpeUnitIdInt         = $vpeUnitId === null ? null : $vpeUnitId->asInt();
            $deliveryTimeId       = $this->findDeliveryTimeId();
            $price                = $variantProduct->getSellingPrice() / (1 + ($variantProduct->getTaxRate() / 100));
            $price                *= $discountFactor;
            $productCustomization = ProductCustomization::create(ProductCustomization::PRICE_TYPE_REPLACING,
                                                                 $price,
                                                                 ProductCustomization::WEIGHT_TYPE_REPLACING,
                                                                 $variantProduct->getWeight(),
                                                                 $variantProduct->getBasepriceFactor(),
                                                                 $deliveryTimeId,
                                                                 $vpeUnitIdInt);
            $variant->changeProductCustomization($productCustomization);
            $ean  = $variantProduct->getManufacturerStandardProductIDType()
                    === 'EAN' ? $variantProduct->getManufacturerStandardProductIDValue() : '';
            $gtin = $variantProduct->getManufacturerStandardProductIDType()
                    === 'GTIN' ? $variantProduct->getManufacturerStandardProductIDValue() : '';
            $asin = $variantProduct->getManufacturerStandardProductIDType()
                    === 'ASIN' ? $variantProduct->getManufacturerStandardProductIDValue() : '';
            $ean  = (empty($ean) && !empty($gtin)) ? $gtin : $ean;
            $gtin = (empty($gtin) && !empty($ean)) ? $ean : $gtin;
            
            $productIdentificationNumbers = ProductIdentificationNumbers::create($variantProduct->getEAN(),
                                                                                 $ean,
                                                                                 $gtin,
                                                                                 $asin);
            $variant->changeProductIdentificationNumbers($productIdentificationNumbers);
            $this->logger->debug("Variant IdNos: {$variantProduct->getEAN()} - {$gtin} - {$asin}");
            $productVariantStock = ProductVariantStock::create($variantProduct->getQuantity());
            $variant->changeStock($productVariantStock);
            
            $imageList   = $this->makeImageListForVariantProduct($variantProduct);
            $imageListId = ImageListId::createAsExisting($imageList->id());
            $variant->changeImageListId($imageListId);
            
            $this->productVariantsWriteService->storeProductVariants($variant);
        }
    }
    
    
    /**
     * @param AfterbuyProduct $afterbuyProduct
     *
     * @return float
     */
    private function getDiscountFactor(AfterbuyProduct $afterbuyProduct): float
    {
        $discountFactor = 1.0;
        $now            = time();
        /** @var Discount $discount */
        foreach ($afterbuyProduct->getDiscounts() as $discount) {
            if ($discount->getStartDate()->getTimestamp() < $now && $discount->getExpireDate()->getTimestamp() > $now) {
                // N. B.: Afterbuy only supports discounts by percentage for Variations
                $discountFactor = 1 - ($discount->getDiscountPercent() / 100);
            }
        }
        
        return $discountFactor;
    }
    
    
    /**
     * @throws ImageRetrievalException
     * @throws ImageListDoesNotExistException
     * @throws StorageOfImageListsFailedException
     */
    private function makeImageListForVariantProduct(AfterbuyProduct $variantProduct): ImageList
    {
        // find/make an ImageList -> ImageListId
        $imageListName = 'AB_' . $variantProduct->getProductID();
        $imageList     = null;
        $allImageLists = $this->imageListReadService->getAllImageLists();
        $oldImageFiles = [];
        foreach ($allImageLists as $allImageList) {
            if ($allImageList->name() === $imageListName) {
                $imageListId = $allImageList->id();
                $imageList   = $this->imageListReadService->getImageListById($imageListId);
                $this->logger->debug("Found image list {$imageListName} with ID {$imageListId}");
                foreach ($imageList->images() as $image) {
                    $oldImageFiles[] = $image->relativePath();
                }
                break;
            }
        }
        if ($imageList === null) {
            $imageListId = $this->imageListWriteService->createImageList($this->imageListFactory->createImageListName($imageListName));
            $imageList   = $this->imageListReadService->getImageListById($imageListId->value());
            $this->logger->debug("Made image list {$imageListName} with ID {$imageListId->value()}");
        }
        // retrieve primary image from $variantProduct
        $subDirectory    = 'ab' . $variantProduct->getProductID();
        $primaryImageUrl = $variantProduct->getImageLargeURL();
        $newImageFiles   = [];
        if (!empty($primaryImageUrl)) {
            $primaryImagePathFileName = $this->importImageFromUrl($primaryImageUrl, $subDirectory);
            $this->logger->debug("Primary image: {$primaryImagePathFileName}");
            $newImageFiles[] = $primaryImagePathFileName;
        }
        // retrieve additional images
        foreach ($variantProduct->getProductPictures() as $productPicture) {
            $productPictureUrl      = $productPicture->getUrl();
            $productPictureFileName = $this->importImageFromUrl($productPictureUrl, $subDirectory);
            $this->logger->debug("Additional image: {$productPictureFileName}");
            $newImageFiles[] = $productPictureFileName;
        }
        $newImages = [];
        $sortOrder = 1;
        foreach ($newImageFiles as $newImageFile) {
            if (!in_array($newImageFile, $oldImageFiles, true)) {
                $this->logger->debug("Adding image as new: {$newImageFile}");
                $newImages[] = $this->makeNewImage($newImageFile, $variantProduct->getName(), $sortOrder++);
            }
        }
        if (!empty($newImages)) {
            $imageList->addNewImages(...$newImages);
        }
        // find images to be deleted from ImageList
        $deleteImages = array_diff($oldImageFiles, $newImageFiles);
        foreach ($deleteImages as $deleteImage) {
            $this->logger->debug("deleting image {$deleteImage}");
            $imageList->removeImage(ImagePath::create($deleteImage));
        }
        
        // store ImageList via ImageListWriteService
        $this->imageListWriteService->storeImageLists($imageList);
        
        return $imageList;
    }
    
    
    /**
     * @param Images $images
     * @param string $relativePath
     *
     * @return bool
     */
    private function isImageInList(Images $images, string $relativePath): bool
    {
        foreach ($images as $image) {
            if ($image->relativePath() === $relativePath) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * @param string $imageFilePath
     * @param string $title
     * @param int    $sortOrder
     *
     * @return NewImage
     */
    private function makeNewImage(string $imageFilePath, string $title, int $sortOrder = 1): NewImage
    {
        $imageTitles    = [];
        $imageAltTitles = [];
        /** @var LanguageCode $languageCode */
        foreach ($this->languageHelper->getLanguageCodes() as $languageCode) {
            $imageTitles[]    = $this->imageListFactory->createImageTitle($languageCode->asString(),
                                                                          $title);
            $imageAltTitles[] = $this->imageListFactory->createImageAltTitle($languageCode->asString(),
                                                                             $title);
        }
        $newImage = $this->imageListFactory->createNewImage($this->imageListFactory->createImagePath($imageFilePath),
                                                            $this->imageListFactory->createImageTitles(...
                                                                $imageTitles),
                                                            $this->imageListFactory->createImageAltTitles(...
                                                                $imageAltTitles),
                                                            $sortOrder);
        
        return $newImage;
    }
    
    
    /**
     * @param string $taxRate
     *
     * @return int|null
     */
    private function findTaxClassId(string $taxRate): ?int
    {
        for ($taxConfigNumber = 1; $taxConfigNumber <= 5; $taxConfigNumber++) {
            $configTaxRate = $this->moduleConfiguration->get("import_tax_rate_{$taxConfigNumber}");
            if ($taxRate === $configTaxRate) {
                return (int)$this->moduleConfiguration->get("import_tax_class_id_{$taxConfigNumber}");
            }
        }
        
        return null;
    }
    
    
    /**
     * @param string $fileName
     *
     * @return bool
     */
    private function imageFileExists(string $fileName): bool
    {
        $imageFolder = DIR_FS_CATALOG . DIR_WS_IMAGES . 'product_images/original_images/';
        
        return file_exists($imageFolder . '/' . $fileName);
    }
    
    
    /**
     * @param string $imageUrl
     * @param string $subDirectory
     *
     * @return string Relative pathname
     * @throws ImageRetrievalException
     */
    public function importImageFromUrl(string $imageUrl, string $subDirectory = ''): string
    {
        $this->logger->debug("fetching image {$imageUrl}");
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Gambio GX4');
        $imageData = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        if ($curlErrno !== CURLE_OK) {
            $message = 'Could not retrieve image file: ' . $curlError;
            $this->logger->error($message);
            throw new ImageRetrievalException($message);
        }
        $this->logger->debug("got " . strlen($imageData) . " bytes");
        
        $imageInfo = getimagesizefromstring($imageData);
        if ($imageInfo === false) {
            throw new ImageRetrievalException('Image cannot be processed');
        }
        
        $imageFolder = DIR_FS_CATALOG . DIR_WS_IMAGES . 'product_images/original_images/' . $subDirectory;
        if (!mkdir($imageFolder, 0777, true) && !is_dir($imageFolder)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $imageFolder));
        }
        
        $tmpFile = tempnam($imageFolder, 'afterbuyimporttmp_');
        if ($tmpFile === false) {
            throw new \RuntimeException('Could not create temporary file');
        }
        file_put_contents($tmpFile, $imageData);
        $urlFileName     = basename($imageUrl);
        $urlFileNamePath = $imageFolder . '/' . $urlFileName;
        if (file_exists($urlFileNamePath)
            && $this->imageFilesAreIdentical($urlFileNamePath,
                                             $imageFolder . '/' . $tmpFile)) {
            unlink($tmpFile);
        } else {
            $this->logger->debug("retrieved variant image $tmpFile => $urlFileNamePath");
            rename($tmpFile, $urlFileNamePath);
        }
        
        return "$subDirectory/$urlFileName";
    }
    
    
    /**
     * @param string $imageUrl
     *
     * @return string
     * @throws ImageRetrievalException
     */
    public function importProductImageFromUrl(string $imageUrl): string
    {
        $this->logger->debug("fetching product image {$imageUrl}");
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Gambio GX4');
        $imageData = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        if ($curlErrno !== CURLE_OK) {
            throw new ImageRetrievalException('Could not retrieve image file: ' . $curlError);
        }
        $this->logger->debug("got " . strlen($imageData) . " bytes");
        
        $imageInfo = getimagesizefromstring($imageData);
        if ($imageInfo === false) {
            throw new ImageRetrievalException('Image cannot be processed');
        }
        
        $imageFolder = DIR_FS_CATALOG . DIR_WS_IMAGES . 'product_images/original_images/';
        $tmpFile     = tempnam($imageFolder, 'afterbuyimporttmp_');
        if ($tmpFile === false) {
            throw new \RuntimeException('Could not create temporary file');
        }
        file_put_contents($tmpFile, $imageData);
        $importFile  = new ExistingFile(new NonEmptyStringType($tmpFile));
        $urlFileName = basename($imageUrl);
        if (preg_match('/.+\.(jpe?g|png|gif)$/i', $urlFileName) === 1) {
            $preferredFileName = new FilenameStringType($urlFileName);
        } else {
            $suffix = '.jpg';
            if ($imageInfo['mime'] === 'image/png') {
                $suffix = '.png';
            } elseif ($imageInfo['mime'] === 'image/gif') {
                $suffix = '.gif';
            }
            $preferredFileName = new FilenameStringType(uniqid('imported_image_', false) . $suffix);
        }
        $finalImageFileName = $this->productWriteService->importProductImageFile($importFile, $preferredFileName);
        unlink($tmpFile);
        
        return $finalImageFileName;
    }
    
    
    /**
     * @param string $fileA
     * @param string $fileB
     *
     * @return bool
     */
    private function imageFilesAreIdentical(string $fileA, string $fileB): bool
    {
        $imageFolder = DIR_FS_CATALOG . DIR_WS_IMAGES . 'product_images/original_images/';
        $hashA       = sha1_file($imageFolder . $fileA);
        $hashB       = sha1_file($imageFolder . $fileB);
        
        return $hashA === $hashB;
    }
    
    
    /**
     * @param string $imageFileName
     *
     * @return void
     */
    private function deleteImageFile(string $imageFileName): void
    {
        $imageFolder       = DIR_FS_CATALOG . DIR_WS_IMAGES . 'product_images/original_images/';
        $fullImageFilePath = $imageFolder . basename($imageFileName);
        if (file_exists($fullImageFilePath)) {
            unlink($fullImageFilePath);
        }
    }
    
    
    /**
     * @param string $unitName
     *
     * @return IdType
     */
    private function findQuantityUnitId(string $unitName): IdType
    {
        $mappedUnitDE   = static::mapQuantityUnit($unitName, 'de');
        $languageCodeDE = new LanguageCode(new StringType('de'));
        
        /** @var QuantityUnitReadService $quantityUnitsReadService */
        $quantityUnitsReadService = StaticGXCoreLoader::getService('QuantityUnitRead');
        $quantityUnits            = $quantityUnitsReadService->getAll();
        /** @var GXEngineQuantityUnit $quantityUnit */
        foreach ($quantityUnits as $quantityUnit) {
            if ($quantityUnit->getName($languageCodeDE) === $mappedUnitDE) {
                $this->logger->debug("Quantity unit {$unitName}/{$mappedUnitDE} has id {$quantityUnit->getId()}");
                
                return new IdType($quantityUnit->getId());
            }
        }
        
        // not found, must be created
        $this->logger->debug("Quantity unit {$unitName}/{$mappedUnitDE} not found, creating");
        /** @var LanguageHelper $languageHelper */
        $languageHelper           = MainFactory::create('LanguageHelper',
                                                        StaticGXCoreLoader::getDatabaseQueryBuilder());
        $quantityUnitWriteService = StaticGXCoreLoader::getService('QuantityUnitWrite');
        $quantityUnit             = $quantityUnitWriteService->createQuantityUnit();
        foreach ($languageHelper->getLanguageCodes() as $languageCode) {
            $newUnitName = static::mapQuantityUnit($unitName, strtolower($languageCode->asString()));
            $quantityUnit->setName(new StringType($newUnitName), $languageCode);
        }
        $quantityUnitWriteService->save($quantityUnit);
        $this->logger->debug("Created quantity unit {$unitName}/{$mappedUnitDE} with id {$quantityUnit->getId()}");
        
        return new IdType($quantityUnit->getId());
    }
    
    
    /**
     * @param string $unitName
     *
     * @return IdType|null
     */
    private function findVpeId(string $unitName): ?IdType
    {
        if (empty($unitName)) {
            return null;
        }
        $mappedUnitDE = static::mapQuantityUnit($unitName, 'de');
        $this->logger->debug("VPE {$unitName} is mapped to {$mappedUnitDE} (de)");
        $languageCodeDE = new LanguageCode(new StringType('de'));
        
        /** @var \VPEReadServiceInterface $vpeReadService */
        $vpeReadService = StaticGXCoreLoader::getService('VPERead');
        $vpes           = $vpeReadService->getAll();
        /** @var \VPEInterface $vpe */
        foreach ($vpes as $vpe) {
            if ($vpe->getName($languageCodeDE) === $mappedUnitDE) {
                $this->logger->debug("VPE {$unitName}/{$mappedUnitDE} has id {$vpe->getId()}");
                
                return new IdType($vpe->getId());
            }
        }
        
        // not found, must be created
        $this->logger->debug("VPE {$unitName}/{$mappedUnitDE} not found, creating");
        /** @var LanguageHelper $languageHelper */
        $languageHelper = MainFactory::create('LanguageHelper',
                                              StaticGXCoreLoader::getDatabaseQueryBuilder());
        /** @var \VPEWriteServiceInterface $vpeWriteService */
        $vpeWriteService = StaticGXCoreLoader::getService('VPEWrite');
        $newVpe          = $vpeWriteService->createVPE();
        /** @var LanguageCode $languageCode */
        foreach ($languageHelper->getLanguageCodes() as $languageCode) {
            $newUnitName = static::mapQuantityUnit($unitName, strtolower($languageCode->asString()));
            $newVpe->setName(new StringType($newUnitName), $languageCode);
        }
        $vpeWriteService->save($newVpe);
        $this->logger->debug("Created VPE {$unitName}/{$mappedUnitDE} with id {$newVpe->getId()}");
        
        return new IdType($newVpe->getId());
    }
    
    
    /**
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findDeliveryTimeId(): int
    {
        $connection       = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $qbuilder         = $connection->createQueryBuilder();
        $shippingStatuses = $qbuilder->from('shipping_status')
            ->select('*')
            ->where('language_id = 2')
            ->orderBy('number_of_days', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return (int)$shippingStatuses[0]['shipping_status_id'];
    }
    
    
    /**
     * @param AfterbuyProduct $afterbuyProduct
     *
     * @return array
     */
    public static function makeVariationSummary(AfterbuyProduct $afterbuyProduct): array
    {
        $variationSummary = [];
        /** @var BaseProduct $baseProduct */
        foreach ($afterbuyProduct->getBaseProducts() as $baseProduct) {
            /** @var BaseProductsRelationData $relationData */
            $relationData = $baseProduct->getBaseProductsRelationData()[0];
            foreach ($relationData->getEBayVariationData() as $EBayVariationDatum) {
                $name  = $EBayVariationDatum->getEBayVariationName();
                $value = $EBayVariationDatum->getEBayVariationValue();
                if (!is_array($variationSummary[$name])) {
                    $variationSummary[$name] = [];
                }
                if (!in_array($value, $variationSummary[$name])) {
                    $variationSummary[$name][] = $value;
                }
            }
        }
        
        return $variationSummary;
    }
    
    
    /**
     * @param string $afterbuyUnit
     * @param string $languageCode
     *
     * @return string
     */
    public static function mapQuantityUnit(string $afterbuyUnit, string $languageCode = 'de'): string
    {
        $unitMap = [
            'L'       => ['de' => 'l', 'en' => 'l'],
            'Kg'      => ['de' => 'kg', 'en' => 'kg'],
            'Stk'     => ['de' => 'Stück', 'en' => 'Pieces'],
            'Pr'      => ['de' => 'Paar', 'en' => 'Pairs'],
            'm'       => ['de' => 'm', 'en' => 'm'],
            'qm'      => ['de' => 'm²', 'en' => 'm²'],
            'Packung' => ['de' => 'Packung', 'en' => 'Packaging'],
            'g'       => ['de' => 'g', 'en' => 'g'],
            'ml'      => ['de' => 'ml', 'en' => 'ml'],
            '100g'    => ['de' => '100 g', 'en' => '100 g'],
            '100ml'   => ['de' => '100 ml', 'en' => '100 ml'],
        ];
        
        if (!array_key_exists($afterbuyUnit, $unitMap)) {
            return $afterbuyUnit;
        }
        
        return $unitMap[$afterbuyUnit][$languageCode] ?? $unitMap[$afterbuyUnit]['en'];
    }
    
    
    /**
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        $this->additionalFieldsHelper->setLogger($logger);
    }
}
