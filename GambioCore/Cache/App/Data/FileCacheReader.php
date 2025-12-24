<?php
/* --------------------------------------------------------------
   FileCacheReader.php 2020-11-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\App\Data;

/**
 * Class FileCacheReader
 *
 * @package Gambio\Core\Cache\App\Data
 */
class FileCacheReader
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
     *
     * @return bool
     */
    public function cacheFileExists(string $cacheNamespace, string $cacheKey): bool
    {
        return file_exists($this->getCacheFilePath($cacheNamespace, $cacheKey));
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @return string
     */
    public function getCacheFileContent(string $cacheNamespace, string $cacheKey): string
    {
        return file_get_contents($this->getCacheFilePath($cacheNamespace, $cacheKey));
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