<?php
/* --------------------------------------------------------------
   ProductServiceFactory.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductServiceFactory
 *
 * @category   System
 * @package    Product
 * @subpackage Factories
 */
class ProductServiceFactory extends AbstractProductServiceFactory
{
    /**
     * Database connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var ProductReadService
     */
    protected $readService;
    
    /**
     * @var ProductWriteService
     */
    protected $writeService;
    
    /**
     * @var ProductObjectService
     */
    protected $objectService;
    
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var ProductListProviderFactory
     */
    protected $productListProviderFactory;
    
    /**
     * @var ProductCategoryLinker
     */
    protected $productCategoryLinker;
    
    /**
     * @var UrlRewriteStorage
     */
    protected $urlRewriteStorage;
    
    /**
     * @var ProductRepositoryReader
     */
    protected $reader;
    
    /**
     * @var ProductRepositoryWriter
     */
    protected $writer;
    
    /**
     * @var ProductRepositoryDeleter
     */
    protected $deleter;
    
    /**
     * @var ProductSettingsRepository
     */
    protected $settingsRepository;
    
    /**
     * @var AddonValueService
     */
    protected $addonValueService;
    
    /**
     * @var ProductImageContainerRepository
     */
    protected $productImageContainerRepository;
    
    /**
     * @var CustomerStatusProvider
     */
    protected $customerStatusProvider;
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    /**
     * @var ProductSettingsRepositoryReader
     */
    protected $settingsReader;
    
    /**
     * @var ProductSettingsRepositoryWriter
     */
    protected $settingsWriter;
    
    /**
     * @var AddonValueStorageFactory
     */
    protected $addonValueStorageFactory;
    
    /**
     * @var ProductImageFileStorage
     */
    protected $productImageStorage;
    
    /**
     * @var EnvProductImageFileStorageSettings
     */
    protected $productImagePathSettings;
    
    /**
     * @var GMSEOBoost_ORIGIN
     */
    protected $urlKeywordsRepairer;
    
    /**
     * @var LegacyProductImageProcessing
     */
    protected $productImageProcessing;
    
    /**
     * @var \DeleteHistoryWriteService
     */
    protected $deleteHistoryService;
    
    
    /**
     * ProductServiceFactory constructor.
     *
     * @param CI_DB_query_builder $db Database connection.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates a new instance of a ProductObjectService object.
     * Consecutive calls provide the same object.
     *
     * @return bool|\ProductObjectService
     */
    public function createProductObjectService()
    {
        if (null === $this->objectService) {
            $this->objectService = MainFactory::create('ProductObjectService', $this->_createProductFactory());
        }
        
        return $this->objectService;
    }
    
    
    /**
     * Creates a new instance of a ProductReadService object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductReadService
     */
    public function createProductReadService()
    {
        if (null === $this->readService) {
            $this->readService = MainFactory::create('ProductReadService',
                                                     $this->createProductRepository(),
                                                     $this->_createProductListProviderFactory(),
                                                     $this->_createProductCategoryLinker(),
                                                     $this->_createUrlRewriteStorage());
        }
        
        return $this->readService;
    }
    
    
    /**
     * Creates a new instance of a ProductWriteService object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductWriteService
     */
    public function createProductWriteService()
    {
        if (null === $this->writeService) {
            $this->writeService = MainFactory::create('ProductWriteService',
                                                      $this->createProductRepository(),
                                                      $this->_createProductImageStorage(),
                                                      $this->_createProductCategoryLinker(),
                                                      $this->_createProductImagePathSettings(),
                                                      $this->_createLanguageProvider(),
                                                      $this->_createUrlKeywordsRepairer(),
                                                      $this->_createDeleteHistoryService());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * Creates a new instance of a ProductRepository object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductRepository
     */
    public function createProductRepository()
    {
        if (null === $this->productRepository) {
            $this->productRepository = MainFactory::create('ProductRepository',
                                                           $this->_createReader(),
                                                           $this->_createWriter(),
                                                           $this->_createDeleter(),
                                                           $this->_createSettingsRepository(),
                                                           $this->_createAddonValueService(),
                                                           $this->_createProductImageContainerRepository(),
                                                           $this->_createUrlRewriteStorage());
        }
        
        return $this->productRepository;
    }
    
    
    /**
     * Creates a new instance of a ProductFactory object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductFactory
     */
    protected function _createProductFactory()
    {
        if (null === $this->productFactory) {
            $this->productFactory = MainFactory::create('ProductFactory');
        }
        
        return $this->productFactory;
    }
    
    
    /**
     * Creates a new instance of a ProductRepositoryReader object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductRepositoryReader
     */
    protected function _createReader()
    {
        if (null === $this->reader) {
            $this->reader = MainFactory::create('ProductRepositoryReader',
                                                $this->db,
                                                $this->_createProductFactory(),
                                                $this->_createCustomerStatusProvider());
        }
        
        return $this->reader;
    }
    
    
    /**
     * Creates a new instance of a CustomerStatusProvider object.
     * Consecutive calls provide the same object.
     *
     * @return \CustomerStatusProvider
     */
    protected function _createCustomerStatusProvider()
    {
        if (null === $this->customerStatusProvider) {
            $this->customerStatusProvider = MainFactory::create('CustomerStatusProvider', $this->db);
        }
        
        return $this->customerStatusProvider;
    }
    
    
    /**
     * Creates a new instance of a ProductRepositoryWriter object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductRepositoryWriter
     */
    protected function _createWriter()
    {
        if (null === $this->writer) {
            $this->writer = MainFactory::create('ProductRepositoryWriter', $this->db, $this->_createLanguageProvider());
        }
        
        return $this->writer;
    }
    
    
    /**
     * Creates a new instance of a LanguageProvider object.
     * Consecutive calls provide the same object.
     *
     * @return \LanguageProvider
     */
    protected function _createLanguageProvider()
    {
        if (null === $this->languageProvider) {
            $this->languageProvider = MainFactory::create('LanguageProvider', $this->db);
        }
        
        return $this->languageProvider;
    }
    
    
    /**
     * Creates a new instance of a ProductRepositoryDeleter object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductRepositoryDeleter
     */
    protected function _createDeleter()
    {
        if (null === $this->deleter) {
            $this->deleter = MainFactory::create('ProductRepositoryDeleter', $this->db, $this->_createDeleteHelper());
        }
        
        return $this->deleter;
    }
    
    
    /**
     * Creates a new instance of a ProductRepositoryDeleteHelper object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductRepositoryDeleteHelper|\ProductRepositoryDeleter
     */
    protected function _createDeleteHelper()
    {
        if (null === $this->deleter) {
            $this->deleter = MainFactory::create('ProductRepositoryDeleteHelper');
        }
        
        return $this->deleter;
    }
    
    
    /**
     * Creates a new instance of a ProductSettingsRepository object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductSettingsRepository
     */
    protected function _createSettingsRepository()
    {
        if (null === $this->settingsRepository) {
            $this->settingsRepository = MainFactory::create('ProductSettingsRepository',
                                                            $this->_createSettingsReader(),
                                                            $this->_createSettingsWriter());
        }
        
        return $this->settingsRepository;
    }
    
    
    /**
     * Creates a new instance of a ProductSettingsRepositoryReader object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductSettingsRepositoryReader
     */
    protected function _createSettingsReader()
    {
        if (null === $this->settingsReader) {
            $this->settingsReader = MainFactory::create('ProductSettingsRepositoryReader',
                                                        $this->db,
                                                        $this->_createProductFactory(),
                                                        $this->_createCustomerStatusProvider());
        }
        
        return $this->settingsReader;
    }
    
    
    /**
     * Creates a new instance of a ProductSettingsRepositoryWriter object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductSettingsRepositoryWriter
     */
    protected function _createSettingsWriter()
    {
        if (null === $this->settingsWriter) {
            $this->settingsWriter = MainFactory::create('ProductSettingsRepositoryWriter',
                                                        $this->db,
                                                        $this->_createCustomerStatusProvider());
        }
        
        return $this->settingsWriter;
    }
    
    
    /**
     * Creates a new instance of a AddonValueService object.
     * Consecutive calls provide the same object.
     *
     * @return \AddonValueService
     */
    protected function _createAddonValueService()
    {
        if (null === $this->addonValueService) {
            $this->addonValueService = MainFactory::create('AddonValueService',
                                                           $this->_createAddonValueStorageFactory());
        }
        
        return $this->addonValueService;
    }
    
    
    /**
     * Creates a new instance of a AddonValueStorageFactory object.
     * Consecutive calls provide the same object.
     *
     * @return \AddonValueStorageFactory
     */
    protected function _createAddonValueStorageFactory()
    {
        if (null === $this->addonValueStorageFactory) {
            $this->addonValueStorageFactory = MainFactory::create('AddonValueStorageFactory', $this->db);
        }
        
        return $this->addonValueStorageFactory;
    }
    
    
    /**
     * Creates a new instance of a ProductImageContainerRepository object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductImageContainerRepository
     */
    protected function _createProductImageContainerRepository()
    {
        if (null === $this->productImageContainerRepository) {
            $this->productImageContainerRepository = MainFactory::create('ProductImageContainerRepository',
                                                                         $this->db,
                                                                         $this->_createLanguageProvider());
        }
        
        return $this->productImageContainerRepository;
    }
    
    
    /**
     * Creates a new instance of a ProductListProviderFactory object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductListProviderFactory
     */
    protected function _createProductListProviderFactory()
    {
        if (null === $this->productListProviderFactory) {
            $this->productListProviderFactory = MainFactory::create('ProductListProviderFactory',
                                                                    $this->createProductRepository(),
                                                                    $this->db);
        }
        
        return $this->productListProviderFactory;
    }
    
    
    /**
     * Creates a new instance of a ProductCategoryLinker object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductCategoryLinker
     */
    protected function _createProductCategoryLinker()
    {
        if (null === $this->productCategoryLinker) {
            $this->productCategoryLinker = MainFactory::create('ProductCategoryLinker', $this->db);
        }
        
        return $this->productCategoryLinker;
    }
    
    
    /**
     * Creates a new instance of a UrlRewriteStorage object.
     * Consecutive calls provide the same object.
     *
     * @return \UrlRewriteStorage
     */
    protected function _createUrlRewriteStorage()
    {
        if (null === $this->urlRewriteStorage) {
            $contentType             = new NonEmptyStringType('product');
            $this->urlRewriteStorage = MainFactory::create('UrlRewriteStorage',
                                                           $contentType,
                                                           $this->db,
                                                           $this->_createLanguageProvider());
        }
        
        return $this->urlRewriteStorage;
    }
    
    
    /**
     * Creates a new instance of a ProductImageFileStorage object.
     * Consecutive calls provide the same object.
     *
     * @return \ProductImageFileStorage
     */
    protected function _createProductImageStorage()
    {
        if (null === $this->productImageStorage) {
            $this->productImageStorage = MainFactory::create('ProductImageFileStorage',
                                                             $this->_createProductImagePathSettings(),
                                                             $this->_createProductImageProcessing());
        }
        
        return $this->productImageStorage;
    }
    
    
    /**
     * Creates a new instance of a LegacyProductImageProcessing object.
     * Consecutive calls provide the same object.
     *
     * @return \LegacyProductImageProcessing
     */
    protected function _createProductImageProcessing()
    {
        if (null === $this->productImageProcessing) {
            $this->productImageProcessing = MainFactory::create('LegacyProductImageProcessing');
        }
        
        return $this->productImageProcessing;
    }
    
    
    /**
     * Creates a new instance of a EnvProductImageFileStorageSettings object.
     * Consecutive calls provide the same object.
     *
     * @return \EnvProductImageFileStorageSettings
     */
    protected function _createProductImagePathSettings()
    {
        if (null === $this->productImagePathSettings) {
            $this->productImagePathSettings = MainFactory::create('EnvProductImageFileStorageSettings');
        }
        
        return $this->productImagePathSettings;
    }
    
    
    /**
     * Creates a new instance of a GMSEOBoost object.
     * Consecutive calls provide the same object.
     *
     * @return \GMSEOBoost_ORIGIN
     */
    protected function _createUrlKeywordsRepairer()
    {
        if (null === $this->urlKeywordsRepairer) {
            $this->urlKeywordsRepairer = MainFactory::create_object('GMSEOBoost', [], true);
        }
        
        return $this->urlKeywordsRepairer;
    }
    
    
    /**
     * Creates a new instance of a DeleteHistoryWriteService object.
     * Consecutive calls provide the same object.
     *
     * @return \DeleteHistoryWriteService
     */
    protected function _createDeleteHistoryService()
    {
        if (null === $this->deleteHistoryService) {
            $this->deleteHistoryService = DeleteHistoryServiceFactory::writeService();
        }
        
        return $this->deleteHistoryService;
    }
}