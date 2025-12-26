<?php
/* --------------------------------------------------------------
   CatalogCategorySync.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs;

use BoolType;
use Category;
use CategoryReadServiceInterface;
use CategorySettings;
use CategoryWriteServiceInterface;
use Doctrine\DBAL\Exception;
use ExistingFile;
use FilenameStringType;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\Exceptions\ImageRetrievalFailedException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\ValueObjects\AfterbuyCatalog;
use IdType;
use NonEmptyStringType;
use ProductWriteServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use StringType;

/**
 * Class CatalogCategorySync
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs
 */
class CatalogCategorySync implements LoggerAwareInterface
{
    /**
     * @var AfterbuyCatalogRepository
     */
    private AfterbuyCatalogRepository $catalogRepository;
    
    
    /**
     * @var CategoryReadServiceInterface
     */
    private CategoryReadServiceInterface $readService;
    
    
    /**
     * @var CategoryWriteServiceInterface
     */
    private CategoryWriteServiceInterface $writeService;
    
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    
    /**
     * @var ProductWriteServiceInterface
     */
    private ProductWriteServiceInterface $productWriteService;
    
    
    /**
     * @param AfterbuyCatalogRepository     $catalogRepository
     * @param CategoryReadServiceInterface  $readService
     * @param CategoryWriteServiceInterface $writeService
     * @param ProductWriteServiceInterface  $productWriteService
     */
    public function __construct(
        AfterbuyCatalogRepository     $catalogRepository,
        CategoryReadServiceInterface  $readService,
        CategoryWriteServiceInterface $writeService,
        ProductWriteServiceInterface  $productWriteService
    ) {
        $this->logger              = new NullLogger();
        $this->catalogRepository   = $catalogRepository;
        $this->readService         = $readService;
        $this->writeService        = $writeService;
        $this->productWriteService = $productWriteService;
    }
    
    
    /**
     * Creates/updates categories to match the catalogs provided by AfterbuyCatalogRepository
     *
     * @param int $parentCategoryId
     * @param int $parentCatalogId
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function syncCategoriesToCatalogs(int $parentCategoryId = 0, int $parentCatalogId = 0): void
    {
        $catalogs = $this->catalogRepository->getCatalogsByParentId($parentCatalogId);
        foreach ($catalogs as $catalog) {
            // find/create category for catalog
            $categoryId = $this->catalogRepository->getCategoryIdByCatalogId($catalog->getCatalogID());
            if ($categoryId === null) {
                // category must be created
                $categoryId = $this->createCategoryFromCatalog($catalog, $parentCategoryId);
                $this->logger->info("Catalog {$catalog->getCatalogID()} ({$catalog->getName()}) has no corresponding category; created it: $categoryId");
            } else {
                $this->logger->info("Catalog {$catalog->getCatalogID()} ({$catalog->getName()}) has corresponding category {$categoryId}");
                $this->updateCategoryFromCatalog($categoryId, $catalog, $parentCategoryId);
            }
            
            // link products into category
            $productIds = $this->catalogRepository->getProductIdsForAfterbuyCatalogId($catalog->getCatalogID());
            $this->logger->debug("Linking products " . implode(', ', $productIds) . " into category {$categoryId}");
            $categoryIdType = new IdType($categoryId);
            foreach ($productIds as $productId) {
                $this->productWriteService->linkProduct(new IdType($productId), $categoryIdType);
            }
            
            // recurse into sub-catalogs
            $this->syncCategoriesToCatalogs($categoryId, $catalog->getCatalogID());
        }
    }
    
    
    /**
     * @param AfterbuyCatalog $catalog
     * @param int             $parentCategoryId
     *
     * @return int ID of newly created category
     * @throws ImageRetrievalFailedException
     */
    protected function createCategoryFromCatalog(AfterbuyCatalog $catalog, int $parentCategoryId = 0): int
    {
        $languageHelper   = new \LanguageHelper(\StaticGXCoreLoader::getDatabaseQueryBuilder());
        $categorySettings = new CategorySettings();
        $categorySettings->setShowSubcategories(new BoolType(true));
        $categorySettings->setShowSubcategoryNames(new BoolType(true));
        $category = new Category($categorySettings);
        
        $category->setParentId(new IdType($parentCategoryId));
        $category->setActive(new BoolType($catalog->isShow()));
        $category->setSortOrder(new \IntType($catalog->getPosition()));
        foreach ($languageHelper->getLanguageCodes() as $languageCode) {
            $category->setName(new StringType($catalog->getName()), $languageCode);
            $category->setHeadingTitle(new StringType($catalog->getName()), $languageCode);
            $category->setDescription(new StringType($catalog->getDescription()), $languageCode);
        }
        $imageUrl = $catalog->getTitlePicture();
        if (!empty($imageUrl)) {
            $imageFileName = $this->importCategoryImageFromUrl($imageUrl);
            $category->setImage(new StringType($imageFileName));
        }
        
        $newCategoryId = $this->writeService->createCategory($category);
        $this->catalogRepository->linkCatalogIdToCategoryId($catalog->getCatalogID(), $newCategoryId);
        
        return $newCategoryId;
    }
    
    
    /**
     * @param int             $categoryId
     * @param AfterbuyCatalog $catalog
     * @param int             $parentCategoryId
     *
     * @return void
     */
    protected function updateCategoryFromCatalog(
        int             $categoryId,
        AfterbuyCatalog $catalog,
        int             $parentCategoryId = 0
    ): void {
        $languageHelper = new \LanguageHelper(\StaticGXCoreLoader::getDatabaseQueryBuilder());
        $category       = $this->readService->getCategoryById(new IdType($categoryId));
        $category->setActive(new BoolType($catalog->isShow()));
        $category->setSortOrder(new \IntType($catalog->getPosition()));
        foreach ($languageHelper->getLanguageCodes() as $languageCode) {
            $category->setName(new StringType($catalog->getName()), $languageCode);
            $category->setHeadingTitle(new StringType($catalog->getName()), $languageCode);
            $category->setDescription(new StringType($catalog->getDescription()), $languageCode);
        }
        $parentCatalogId = $catalog->getParentID();
        if (!empty($parentCatalogId)) {
            $catalogParentCategoryId = $this->catalogRepository->getCategoryIdByCatalogId($parentCatalogId);
            $parentCategoryId        = $catalogParentCategoryId ?? $parentCategoryId;
        }
        $category->setParentId(new IdType($parentCategoryId));
        $this->writeService->updateCategory($category);
        $this->writeService->moveCategory(new IdType($categoryId), new IdType($parentCategoryId));
    }
    
    
    /**
     * @param string $imageUrl
     *
     * @return string
     * @throws ImageRetrievalFailedException
     */
    public function importCategoryImageFromUrl(string $imageUrl): string
    {
        $this->logger->notice("fetching category image {$imageUrl}");
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Gambio GX4');
        $imageData = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        if ($curlErrno !== CURLE_OK) {
            throw new ImageRetrievalFailedException('Could not retrieve category image file: ' . $curlError);
        }
        
        $imageInfo = getimagesizefromstring($imageData);
        if ($imageInfo === false) {
            $this->logger->warning("Could not import category image, resource at {$imageUrl} is not a valid image.");
            throw new ImageRetrievalFailedException('Image cannot be processed');
        }
        
        $imageFolder = DIR_FS_CATALOG . DIR_WS_IMAGES . 'categories/';
        $tmpFile     = tempnam($imageFolder, 'afterbuyimporttmp_');
        if ($tmpFile === false) {
            throw new ImageRetrievalFailedException('Could not create temporary file for category image');
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
        $finalImageFileName = $this->writeService->importCategoryImageFile($importFile, $preferredFileName);
        unlink($tmpFile);
        
        return $finalImageFileName;
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
