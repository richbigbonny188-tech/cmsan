<?php
/**
 * DeleteServiceImageListFactory.php 2023-03-06
 * Last Modified: 1/24/20, 4:45 PM
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2023 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\ProductImageList\DeleteService\Factories;

use CI_DB_query_builder;
use Gambio\ProductImageList\DeleteService\Interfaces\ProductImageListDeleteServiceFactoryInterface;
use Gambio\ProductImageList\DeleteService\Interfaces\DeleteServiceDeleterInterface;
use Gambio\ProductImageList\DeleteService\Interfaces\DeleteRepositoryInterface;
use Gambio\ProductImageList\DeleteService\Repositories\DeleteRepository;
use Gambio\ProductImageList\DeleteService\Repositories\DeleteServiceDeleterDatabase;
use Gambio\ProductImageList\Interfaces\ProductImageListDeleteServiceInterface;
use Gambio\ProductImageList\ProductImageListDeleteService;

class ProductImageListProductDeleteServiceFactory implements ProductImageListDeleteServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var DeleteServiceDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var DeleteRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var ProductImageListDeleteServiceInterface
     */
    protected $service;
    
    
    /**
     * ProductImageListProductDeleteServiceFactory constructor.
     *
     * @param CI_DB_query_builder $query_builder
     */
    public function __construct(
        CI_DB_query_builder $query_builder
    ) {
        $this->queryBuilder = $query_builder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createService() : ProductImageListDeleteServiceInterface
    {
        if ($this->service === null) {
            $this->service = new ProductImageListDeleteService($this->getRepository());
        }
        
        return $this->service;
    }
    
    
    protected function getRepository() : DeleteRepositoryInterface
    {
        if ($this->repository === null) {
            $this->repository = new DeleteRepository($this->getDeleter());
        }
        
        return $this->repository;
    }
    
    
    protected function getDeleter() : DeleteServiceDeleterInterface
    {
        if ($this->deleter === null) {
            $this->deleter = new DeleteServiceDeleterDatabase($this->queryBuilder);
        }
        
        return $this->deleter;
    }
    
}