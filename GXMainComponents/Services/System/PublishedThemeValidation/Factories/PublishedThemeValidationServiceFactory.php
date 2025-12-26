<?php
/* --------------------------------------------------------------
  PublishedThemeValidationServiceFactory.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class PublishedThemeValidationServiceFactory
 */
class PublishedThemeValidationServiceFactory implements PublishedThemeValidationServiceFactoryInterface
{
    /**
     * @var PublishedThemeValidationServiceInterface
     */
    protected $service;
    
    /**
     * @var PublishedThemePathRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    /**
     * @var ShopPathsInterface
     */
    protected $shopPaths;
    
    /**
     * @var PublishedThemePathCacheWriterInterface
     */
    protected $writer;
    
    /**
     * @var PublishedThemePathCacheReaderInterface
     */
    protected $reader;
    
    /**
     * @var PublishedThemePathFactoryInterface
     */
    protected $factory;
    
    /**
     * @var PublishedThemeRemoverInterface
     */
    protected $remover;
    /**
     * @var CacheControl
     */
    protected $cacheControl;
    
    
    /**
     * PublishedThemeValidationServiceFactory constructor.
     *
     * @param FilesystemAdapter  $filesystem
     * @param ShopPathsInterface $shopPaths
     * @param CacheControl       $cacheControl
     */
    public function __construct(FilesystemAdapter $filesystem, ShopPathsInterface $shopPaths, CacheControl $cacheControl)
    {
        $this->filesystem   = $filesystem;
        $this->shopPaths    = $shopPaths;
        $this->cacheControl = $cacheControl;
    }
    
    
    /**
     * @inheritDoc
     */
    public function service(): PublishedThemeValidationServiceInterface
    {
        if ($this->service === null) {
            
            $this->service = MainFactory::create(PublishedThemeValidationService::class, $this->repository(), $this->shopPaths, $this->cacheControl);
        }
        
        return $this->service;
    }
    
    
    /**
     * @return PublishedThemePathRepositoryInterface
     */
    protected function repository(): PublishedThemePathRepositoryInterface
    {
        if ($this->repository === null) {
    
            $this->repository = MainFactory::create(PublishedThemePathRepository::class,
                                                    $this->reader(),
                                                    $this->writer(),
                                                    $this->factory(),
                                                    $this->remover(),
                                                    $this->shopPaths);
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return PublishedThemePathCacheWriterInterface
     */
    protected function writer(): PublishedThemePathCacheWriterInterface
    {
        if ($this->writer === null) {
            
            $this->writer = MainFactory::create(PublishedThemePathCacheWriter::class, $this->filesystem, $this->shopPaths);
        }
        
        return $this->writer;
    }
    
    
    /**
     * @return PublishedThemePathCacheReaderInterface
     */
    protected function reader(): PublishedThemePathCacheReaderInterface
    {
        if ($this->reader === null) {
            
            $this->reader = MainFactory::create(PublishedThemePathCacheReader::class, $this->filesystem, $this->shopPaths);
        }
        
        return $this->reader;
    }
    
    
    /**
     * @return PublishedThemePathFactoryInterface
     */
    protected function factory(): PublishedThemePathFactoryInterface
    {
        if ($this->factory === null) {
            
            $this->factory = MainFactory::create(PublishedThemePathFactory::class);
        }
        
        return $this->factory;
    }
    
    
    /**
     * @return PublishedThemeRemoverInterface
     */
    protected function remover(): PublishedThemeRemoverInterface
    {
        if ($this->remover === null) {
            
            $this->remover = MainFactory::create(PublishedThemeRemover::class, $this->filesystem, $this->shopPaths);
        }
        
        return $this->remover;
    }
}