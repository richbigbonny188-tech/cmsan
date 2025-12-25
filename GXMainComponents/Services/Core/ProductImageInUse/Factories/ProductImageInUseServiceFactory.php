<?php
/*--------------------------------------------------------------
   ProductImageInUseServiceFactory.php 2020-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Doctrine\DBAL\Connection;

/**
 * Class ProductImageInUseServiceFactory
 */
class ProductImageInUseServiceFactory
{
    /**
     * @var ProductImageInUseServiceInterface
     */
    protected $service;
    
    /**
     * @var ProductImageRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var ProductImageReader
     */
    protected $reader;
    
    /**
     * @var ProductImageBaseNameFactory
     */
    protected $factory;
    
    
    /**
     * @return ProductImageInUseServiceInterface
     */
    public function service(): ProductImageInUseServiceInterface
    {
        if ($this->service === null) {
            
            $this->service = new ProductImageInUseService($this->repository());
        }
        
        return $this->service;
    }
    
    
    /**
     * @return ProductImageRepositoryInterface
     */
    protected function repository(): ProductImageRepositoryInterface
    {
        if ($this->repository === null) {
    
            $this->repository = new ProductImageRepository($this->reader(), $this->factory());
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return ProductImageReaderInterface
     */
    protected function reader(): ProductImageReaderInterface
    {
        if ($this->reader === null) {
    
            $connection   = LegacyDependencyContainer::getInstance()->get(Connection::class);
            $this->reader = new ProductImageReader($connection);
        }
        
        return $this->reader;
    }
    
    
    /**
     * @return ProductImageBaseNameFactoryInterface
     */
    protected function factory(): ProductImageBaseNameFactoryInterface
    {
        if ($this->factory === null) {
            
            $this->factory = new ProductImageBaseNameFactory(new OriginalImagesPath);
        }
        
        return $this->factory;
    }
}