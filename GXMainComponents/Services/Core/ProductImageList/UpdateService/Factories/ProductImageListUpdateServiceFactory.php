<?php
/* --------------------------------------------------------------
  ProductImageListUpdateServiceFactory.php 2020-01-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Factories;

use CI_DB_query_builder;
use Gambio\ProductImageList\Interfaces\ProductImageListUpdateServiceInterface;
use Gambio\ProductImageList\ProductImageListUpdateService;
use Gambio\ProductImageList\UpdateService\Interfaces\ProductImageListUpdateServiceFactoryInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateServiceDatabaseWriterInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateServiceRepositoryInterface;
use Gambio\ProductImageList\UpdateService\Repositories\UpdateServiceDatabaseWriter;
use Gambio\ProductImageList\UpdateService\Repositories\UpdateServiceRepository;
use LanguageProvider;
use LanguageProviderInterface;

/**
 * Class ProductImageListUpdateServiceFactory
 * @package Gambio\ProductImageList\UpdateService\Factories
 */
class ProductImageListUpdateServiceFactory implements ProductImageListUpdateServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var ProductImageListUpdateService
     */
    protected $service;
    
    /**
     * @var UpdateServiceRepository
     */
    protected $repository;
    
    /**
     * @var UpdateServiceDatabaseWriter
     */
    protected $writer;
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * ProductImageListUpdateServiceFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function service(): ProductImageListUpdateServiceInterface
    {
        if ($this->service === null) {
            
            $this->service = new ProductImageListUpdateService($this->repository());
        }
        
        return $this->service;
    }
    
    
    /**
     * @return UpdateServiceRepositoryInterface
     */
    protected function repository(): UpdateServiceRepositoryInterface
    {
        if ($this->repository === null) {
            
            $this->repository = new UpdateServiceRepository($this->writer());
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return UpdateServiceDatabaseWriterInterface
     */
    protected function writer(): UpdateServiceDatabaseWriterInterface
    {
        if ($this->writer === null) {
            
            $this->writer = new UpdateServiceDatabaseWriter($this->queryBuilder, $this->languageProvider());
        }
        
        return $this->writer;
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