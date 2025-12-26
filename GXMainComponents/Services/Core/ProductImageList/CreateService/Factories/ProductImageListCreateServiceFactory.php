<?php
/* --------------------------------------------------------------
  interfaceProductImageListCreateServiceFactory.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\CreateService\Factories;

use CI_DB_query_builder;
use Gambio\ProductImageList\CreateService\Interfaces\CreateServiceDatabaseWriterInterface;
use Gambio\ProductImageList\CreateService\Interfaces\CreateServiceRepositoryInterface;
use Gambio\ProductImageList\CreateService\Interfaces\ProductImageListCreateServiceFactoryInterface;
use Gambio\ProductImageList\CreateService\Repositories\CreateServiceDatabaseWriter;
use Gambio\ProductImageList\CreateService\Repositories\CreateServiceRepository;
use Gambio\ProductImageList\Interfaces\ProductImageListCreateServiceInterface;
use Gambio\ProductImageList\ProductImageListCreateService;
use LanguageProvider;
use LanguageProviderInterface;

/**
 * Class interfaceProductImageListCreateServiceFactory
 * @package Gambio\ProductImageList\CreateService\Factories
 */
class ProductImageListCreateServiceFactory implements ProductImageListCreateServiceFactoryInterface
{
    /**
     * @var ProductImageListCreateService
     */
    protected $service;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var CreateServiceRepository
     */
    protected $repository;
    
    /**
     * @var CreateServiceDatabaseWriter
     */
    protected $writer;
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * ProductImageListCreateServiceFactory constructor.
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
    public function service(): ProductImageListCreateServiceInterface
    {
        if ($this->service === null) {
            
            $this->service = new ProductImageListCreateService($this->repository());
        }
        
        return $this->service;
    }
    
    /**
     * @return CreateServiceRepositoryInterface
     */
    protected function repository(): CreateServiceRepositoryInterface
    {
    	if($this->repository === null) {
    	
    		$this->repository = new CreateServiceRepository($this->writer());
    	}
    	
    	return $this->repository;
    }
    
    /**
     * @return CreateServiceDatabaseWriterInterface
     */
    protected function writer(): CreateServiceDatabaseWriterInterface
    {
    	if($this->writer === null) {
    	
    		$this->writer = new CreateServiceDatabaseWriter($this->queryBuilder, $this->languageProvider());
    	}
    	
    	return $this->writer;
    }
    
    /**
     * @return LanguageProviderInterface
     */
    protected function languageProvider(): LanguageProviderInterface
    {
    	if($this->languageProvider === null) {
    	
    		$this->languageProvider = new LanguageProvider($this->queryBuilder);
    	}
    	
    	return $this->languageProvider;
    }
}