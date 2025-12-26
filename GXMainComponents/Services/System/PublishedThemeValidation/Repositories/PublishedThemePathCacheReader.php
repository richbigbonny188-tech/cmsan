<?php
/* --------------------------------------------------------------
  PublishedThemePathCacheReader.php 2022-04-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use League\Flysystem\FilesystemException;

/**
 * Class PublishedThemePathCacheReader
 */
class PublishedThemePathCacheReader implements PublishedThemePathCacheReaderInterface
{
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    /**
     * @var ShopPathsInterface
     */
    protected $shopPaths;
    
    
    /**
     * PublishedThemePathCacheReader constructor.
     *
     * @param FilesystemAdapter  $filesystem
     * @param ShopPathsInterface $shopPaths
     */
    public function __construct(FilesystemAdapter $filesystem, ShopPathsInterface $shopPaths)
    {
        $this->filesystem = $filesystem;
        $this->shopPaths  = $shopPaths;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCacheFileData(): string
    {
        try {
            return (string)$this->filesystem->read($this->shopPaths->cacheFilePath());
        } catch (FilesystemException $e) {
            throw new CacheFileNotFoundException($e->getMessage());
        }
    }
}