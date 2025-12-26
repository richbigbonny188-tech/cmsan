<?php
/* --------------------------------------------------------------
  ProductImageListReadServiceFactory.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ReadService\Factories;

use CI_DB_query_builder;
use Gambio\ProductImageList\Image\Builders\ImageBuilder;
use Gambio\ProductImageList\ImageList\Builders\ImageListBuilder;
use Gambio\ProductImageList\Interfaces\ProductImageListReadServiceInterface;
use Gambio\ProductImageList\ProductImageListReadService;
use Gambio\ProductImageList\ReadService\Interfaces\ProductImageListReadServiceFactoryInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceDatabaseReaderInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceImageListsCollectionFactoryInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceRepositoryInterface;
use Gambio\ProductImageList\ReadService\Repositories\ReadServiceDatabaseReader;
use Gambio\ProductImageList\ReadService\Repositories\ReadServiceRepository;
use LanguageProvider;
use LanguageProviderInterface;

/**
 * Class ProductImageListReadServiceFactory
 * @package Gambio\ProductImageList\ReadService\Factories
 */
class ProductImageListReadServiceFactory implements ProductImageListReadServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected $shopWebPath;
    
    /**
     * @var string
     */
    protected $shopLocalPath;
    
    /**
     * @var ProductImageListReadService
     */
    protected $service;
    
    /**
     * @var ReadServiceRepository
     */
    protected $repository;
    
    /**
     * @var ReadServiceImageListsCollectionFactory
     */
    protected $factory;
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    /**
     * @var ReadServiceDatabaseReader
     */
    protected $reader;
    
    
    /**
     * ProductImageListReadServiceFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     * @param string              $shopWebPath
     * @param string              $shopLocalPath
     */
    public function __construct(CI_DB_query_builder $queryBuilder, string $shopWebPath, string $shopLocalPath)
    {
        $this->queryBuilder  = $queryBuilder;
        $this->shopWebPath   = $shopWebPath;
        $this->shopLocalPath = $shopLocalPath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function service(): ProductImageListReadServiceInterface
    {
        if ($this->service === null) {
            
            $this->service = new ProductImageListReadService($this->repository());
        }
        
        return $this->service;
    }
    
    
    /**
     * @return ReadServiceRepositoryInterface
     */
    protected function repository(): ReadServiceRepositoryInterface
    {
        if ($this->repository === null) {
            
            $this->repository = new ReadServiceRepository($this->reader(), $this->factory());
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return ReadServiceDatabaseReaderInterface
     */
    protected function reader(): ReadServiceDatabaseReaderInterface
    {
        if ($this->reader === null) {
            
            $this->reader = new ReadServiceDatabaseReader($this->queryBuilder);
        }
        
        return $this->reader;
    }
    
    /**
     * @return ReadServiceImageListsCollectionFactoryInterface
     */
    protected function factory(): ReadServiceImageListsCollectionFactoryInterface
    {
        if ($this->factory === null) {
    
            $this->factory = new ReadServiceImageListsCollectionFactory($this->languageProvider(),
                                                                        ImageBuilder::create(),
                                                                        ImageListBuilder::create(),
                                                                        $this->shopWebPath,
                                                                        $this->shopLocalPath);
        }
        
        return $this->factory;
    }
    
    
    /**
     * @return LanguageProviderInterface
     */
    protected function languageProvider(): LanguageProviderInterface
    {
        if ($this->languageProvider === null) {
            
            $this->languageProvider = new LanguageProvider($this->queryBuilder);
        }
        
        return $this->languageProvider;
    }
}