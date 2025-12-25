<?php
/* --------------------------------------------------------------
  PublishedThemePathRepository.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class PublishedThemeRepository
 */
class PublishedThemePathRepository implements PublishedThemePathRepositoryInterface
{
    /**
     * @var PublishedThemePathCacheReaderInterface
     */
    protected $reader;
    
    /**
     * @var PublishedThemePathCacheWriterInterface
     */
    protected $writer;
    
    /**
     * @var PublishedThemePathFactoryInterface
     */
    protected $factory;
    
    /**
     * @var ShopPathsInterface
     */
    protected $shopBaseUrl;
    /**
     * @var PublishedThemeRemoverInterface
     */
    protected $remover;
    
    
    /**
     * PublishedThemeRepository constructor.
     *
     * @param PublishedThemePathCacheReaderInterface $reader
     * @param PublishedThemePathCacheWriterInterface $writer
     * @param PublishedThemePathFactoryInterface     $factory
     * @param PublishedThemeRemoverInterface         $remover
     * @param ShopPathsInterface                     $shopBaseUrl
     */
    public function __construct(
        PublishedThemePathCacheReaderInterface $reader,
        PublishedThemePathCacheWriterInterface $writer,
        PublishedThemePathFactoryInterface $factory,
        PublishedThemeRemoverInterface $remover,
        ShopPathsInterface $shopBaseUrl
    ) {
        $this->reader      = $reader;
        $this->writer      = $writer;
        $this->factory     = $factory;
        $this->remover     = $remover;
        $this->shopBaseUrl = $shopBaseUrl;
    }
    
    
    /**
     * @inheritDoc
     */
    public function cacheFile(): PublishedThemePathCacheInterface
    {
        try {
            $cacheFileData = $this->reader->getCacheFileData();
        } catch (CacheFileNotFoundException $cacheFileNotFoundException) {
            $cacheFileData = $this->shopBaseUrl->webPath();
            $this->store();
        } finally {
            return $this->factory->create($cacheFileData);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function store(): void
    {
        $this->writer->store($this->shopBaseUrl->webPath());
    }
    
    
    public function removePublishedTheme(): void
    {
        $this->remover->removePublishedTheme();
    }
}