<?php
/* --------------------------------------------------------------
   FileCacheWriter.php 2020-11-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\App\Data;

use Gambio\Core\Cache\App\Data\Exceptions\FileCreationFailed;
use Gambio\Core\Cache\App\Data\Exceptions\FileDeletionFailed;

/**
 * Class FileCacheWriter
 *
 * @package Gambio\Core\Cache\App\Data
 */
class FileCacheWriter
{
    /**
     * @var string
     */
    private $pathToCacheDirectory;
    
    
    /**
     * FileCacheReader constructor.
     *
     * @param string $pathToCacheDirectory
     */
    public function __construct(string $pathToCacheDirectory)
    {
        $this->pathToCacheDirectory = $pathToCacheDirectory;
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     * @param string $content
     *
     * @throws FileCreationFailed
     */
    public function createCacheFile(string $cacheNamespace, string $cacheKey, string $content): void
    {
        $cacheFile = $this->getCacheFilePath($cacheNamespace, $cacheKey);
        if (@file_put_contents($cacheFile, $content) === false) {
            throw FileCreationFailed::forCacheFile($cacheFile);
        }
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @throws FileDeletionFailed
     */
    public function deleteCacheFile(string $cacheNamespace, string $cacheKey): void
    {
        $cacheFile = $this->getCacheFilePath($cacheNamespace, $cacheKey);
        if (is_file($cacheFile) && @unlink($cacheFile) === false) {
            throw FileDeletionFailed::forCacheFile($cacheFile);
        }
    }
    
    
    /**
     * @param string $cacheNamespace
     *
     * @throws FileDeletionFailed
     */
    public function deleteAllCacheFilesByNamespace(string $cacheNamespace): void
    {
        $cacheFiles = glob($this->pathToCacheDirectory . '/' . $cacheNamespace . '-*.cache');
        foreach ($cacheFiles as $cacheFile) {
            if (is_file($cacheFile) && @unlink($cacheFile) === false) {
                throw FileDeletionFailed::forCacheFile($cacheFile);
            }
        }
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @return string
     */
    private function getCacheFilePath(string $cacheNamespace, string $cacheKey): string
    {
        return $this->pathToCacheDirectory . '/' . $cacheNamespace . '-' . md5($cacheKey) . '.cache';
    }
}