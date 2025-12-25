<?php
/* --------------------------------------------------------------
   ProductMainImageInUseServiceFactory.php 2023-11-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ProductMainImageInUseServiceFactory
 *
 * @package Gambio\MainComponents\Services\Core\ProductImageInUse\Factories
 */
class ProductMainImageInUseServiceFactory
{
    /**
     * @var ProductMainImageInUseServiceInterface|null
     */
    protected ?ProductMainImageInUseServiceInterface $mainImageInUseService = null;
    
    
    /**
     * @var ProductMainImageRepositoryInterface|null
     */
    protected ?ProductMainImageRepositoryInterface $mainImageRepository = null;
    
    
    /**
     * @var ProductMainImageReaderInterface|null
     */
    protected ?ProductMainImageReaderInterface $mainImageReader = null;
    
    
    /**
     * @var ProductImageFactory|null
     */
    protected ?ProductImageFactory $productImageFactory = null;
    
    
    /**
     * @return ProductMainImageInUseServiceInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createService(): ProductMainImageInUseServiceInterface
    {
        if ($this->mainImageInUseService === null) {
            $this->mainImageInUseService = new ProductMainImageInUseService($this->createRepository());
        }
        
        return $this->mainImageInUseService;
    }
    
    
    /**
     * @return ProductMainImageRepositoryInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function createRepository(): ProductMainImageRepositoryInterface
    {
        if ($this->mainImageRepository === null) {
            $this->mainImageRepository = new ProductMainImageRepository($this->createReader());
        }
        
        return $this->mainImageRepository;
    }
    
    
    /**
     * @return ProductMainImageReaderInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function createReader(): ProductMainImageReaderInterface
    {
        if ($this->mainImageReader === null) {
            $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);
            $factory    = $this->createProductImageFactory();
            
            $this->mainImageReader = new ProductMainImageReader($connection, $factory);
        }
        
        return $this->mainImageReader;
    }
    
    
    /**
     * @return ProductImageFactory
     */
    protected function createProductImageFactory(): ProductImageFactory
    {
        if ($this->productImageFactory === null) {
            $this->productImageFactory = new ProductImageFactory();
        }
        
        return $this->productImageFactory;
    }
}