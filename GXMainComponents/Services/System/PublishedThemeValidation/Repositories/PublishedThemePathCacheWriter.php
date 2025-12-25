<?php
/* --------------------------------------------------------------
  PublishedThemePathCacheWriter.php 2022-04-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use League\Flysystem\FilesystemException;

/**
 * Class PublishedThemePathCacheWriter
 */
class PublishedThemePathCacheWriter implements PublishedThemePathCacheWriterInterface
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
     * PublishedThemePathCacheWriter constructor.
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
    public function store(string $data): void
    {
        $path = $this->shopPaths->cacheFilePath();
    
        try {
            if (!$this->filesystem->has($path)) {
                $this->filesystem->write($path, $data);
            }
        } catch (FilesystemException $e) {
        }
    }
}