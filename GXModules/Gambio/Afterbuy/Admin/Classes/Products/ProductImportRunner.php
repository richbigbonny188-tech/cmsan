<?php
/* --------------------------------------------------------------
   ProductImportRunner.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products;

use EntityNotFoundException;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\CreationOfAdditionalFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\CreationOfAdditionalProductFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\DeletionOfAdditionalProductFieldsFailedException;
use GambioAfterbuyConfigurationStorage;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\AfterbuyXMLService;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\Exceptions\XMLException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\AfterbuyCatalogRepository;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions\ProductImportException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions\UnsupportedProductTypeException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AfterbuyProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ProductImportRunnerStatus;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class ProductImportRunner
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products
 */
class ProductImportRunner implements LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    
    /**
     * @var GambioAfterbuyConfigurationStorage
     */
    private GambioAfterbuyConfigurationStorage $configurationStorage;
    
    
    /**
     * @var AfterbuyXMLService
     */
    private AfterbuyXMLService $XMLService;
    
    
    /**
     * @var AfterbuyCatalogRepository
     */
    private AfterbuyCatalogRepository $catalogRepository;
    
    
    /**
     * @var AfterbuyProductImporter
     */
    private AfterbuyProductImporter $importer;
    
    
    /**
     * @param GambioAfterbuyConfigurationStorage $configurationStorage
     * @param AfterbuyXMLService                 $XMLService
     * @param AfterbuyCatalogRepository          $catalogRepository
     * @param AfterbuyProductImporter            $importer
     */
    public function __construct(
        GambioAfterbuyConfigurationStorage $configurationStorage,
        AfterbuyXMLService                 $XMLService,
        AfterbuyCatalogRepository          $catalogRepository,
        AfterbuyProductImporter            $importer
    ) {
        $this->logger               = new NullLogger();
        $this->configurationStorage = $configurationStorage;
        $this->XMLService           = $XMLService;
        $this->catalogRepository    = $catalogRepository;
        $this->importer             = $importer;
    }
    
    
    /**
     * @return ProductImportRunnerStatus
     * @throws InsertionOfProductVariantsFailed
     * @throws ProductImportException
     * @throws XMLException
     * @throws EntityNotFoundException
     * @throws StorageOfProductVariantsFailed
     * @throws CreationOfAdditionalFieldFailedException
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws DeletionOfAdditionalProductFieldsFailedException
     */
    public function run(): ProductImportRunnerStatus
    {
        $defaultCategoryId     = (int)$this->configurationStorage->get('import_products_default_category');
        $sinceTimestamp        = (int)$this->configurationStorage->get('import_since_timestamp');
        $sinceDateTime         = new \DateTimeImmutable("@{$sinceTimestamp}");
        $lastPage              = (int)$this->configurationStorage->get('import_last_page_number');
        $lastProductId         = (int)$this->configurationStorage->get('import_last_product_id');
        $productLevelThreshold = (int)$this->configurationStorage->get('import_products_level_threshold');
        $dealerGroupId         = (int)$this->configurationStorage->get('import_products_dealer_group');
        $dealerGroupId         = $dealerGroupId > 0 ? $dealerGroupId : null;
        $maxProductsPerRun     = (int)$this->configurationStorage->get('import_max_products_per_run');
        
        $this->XMLService->setLogger($this->logger);
        $this->importer->setLogger($this->logger);
        
        $this->logger->info("Starting product import run");
        
        if ($lastPage === 0 && $lastProductId > 0) {
            // continue non-paginated run
            $getProductsResult = $this->XMLService->getShopProductsModifiedSinceRange($sinceDateTime,
                                                                                      $maxProductsPerRun,
                                                                                      $lastProductId);
        } elseif ($lastPage > 0) {
            // continue paginated run
            $getProductsResult = $this->XMLService->getShopProductsModifiedSinceRange($sinceDateTime,
                                                                                      $maxProductsPerRun,
                                                                                      0,
                                                                                      false,
                                                                                      $lastPage + 1);
        } elseif ($lastPage === 0 && $lastProductId === 0) {
            // start paginated run
            $getProductsResult = $this->XMLService->getShopProductsModifiedSinceRange($sinceDateTime,
                                                                                      $maxProductsPerRun,
                                                                                      0,
                                                                                      false,
                                                                                      1);
        }
        
        /** @var AfterbuyProduct $importAfterbuyProduct */
        foreach ($getProductsResult->getProducts() as $importAfterbuyProduct) {
            $this->logger->debug("Importing {$importAfterbuyProduct->getProductID()}");
            //$productCategoryIds = $this->catalogRepository->getCategoryIdsForAfterbuyProductId($importAfterbuyProduct->getProductID());
            $productCategoryIds = [];
            foreach ($importAfterbuyProduct->getCatalogs() as $catalogId) {
                $categoryId = $this->catalogRepository->getCategoryIdByCatalogId($catalogId);
                if ($categoryId !== null) {
                    $productCategoryIds[] = $categoryId;
                }
            }
            $this->logger->debug("Product {$importAfterbuyProduct->getProductID()}, Categories " . implode(", ",
                                                                                                           $productCategoryIds));
            if ($defaultCategoryId < 0) {
                $this->logger->info("Product {$importAfterbuyProduct->getProductID()} has no associated catalog, no default category set => not importing");
                continue;
            }
            $importCategoryIds = empty($productCategoryIds) ? [$defaultCategoryId] : $productCategoryIds;
            try {
                $gambioProductId = $this->importer->importProduct($importAfterbuyProduct,
                                                                  $importCategoryIds,
                                                                  $productLevelThreshold,
                                                                  $dealerGroupId);
                $this->logger->debug("Imported Afterbuy product {$importAfterbuyProduct->getProductID()} as {$gambioProductId} into categories "
                                     . implode(', ', $importCategoryIds));
            } catch (UnsupportedProductTypeException $upe) {
                $this->logger->debug("Product has BaseProductFlag {$importAfterbuyProduct->getBaseProductFlag()}, skipping");
                continue;
            }
        }
        
        if ($getProductsResult->isHasMoreProducts()) {
            $this->configurationStorage->set('import_last_product_id', $getProductsResult->getLastProductId());
            $this->configurationStorage->set('import_last_page_number', $getProductsResult->getPageNumber());
            $status = new ProductImportRunnerStatus($sinceDateTime,
                                                    $getProductsResult->getLastProductId(),
                                                    (int)$getProductsResult->getPageNumber(),
                                                    (int)$getProductsResult->getTotalNumberOfPages());
            $this->logger->info("Product import step completed, more remaining (page {$getProductsResult->getPageNumber()} of {$getProductsResult->getTotalNumberOfPages()})");
        } else {
            $now = time();
            $this->configurationStorage->set('import_last_product_id', 0);
            $this->configurationStorage->set('import_last_page_number', 0);
            $this->configurationStorage->set('import_since_timestamp', $now);
            $status = new ProductImportRunnerStatus(new \DateTimeImmutable("@{$now}"),
                                                    $getProductsResult->getLastProductId(),
                                                    (int)$getProductsResult->getPageNumber(),
                                                    (int)$getProductsResult->getTotalNumberOfPages());
            $this->logger->info("Product import finished (" . (int)$getProductsResult->getTotalNumberOfPages()
                                . " pages)");
        }
        
        return $status;
    }
    
    
    /**
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
