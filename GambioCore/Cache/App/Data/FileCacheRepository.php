<?php
/* --------------------------------------------------------------
   FileCacheRepository.php 2023-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\App\Data;

use Gambio\Core\Cache\Model\CachedData;
use Gambio\Core\Cache\Services\Exceptions\InvalidCacheKeyException;
use Gambio\Core\Logging\LoggerBuilder;
use Psr\Log\LoggerInterface;

/**
 * Class FileCacheRepository
 *
 * @package Gambio\Core\Cache\App\Data
 */
class FileCacheRepository
{
    /**
     * @var FileCacheReader
     */
    private $reader;
    
    /**
     * @var FileCacheWriter
     */
    private $writer;
    
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * @var string[]
     */
    private $allowedClasses;
    
    
    /**
     * FileCacheRepository constructor.
     *
     * @param FileCacheReader $reader
     * @param FileCacheWriter $writer
     * @param LoggerBuilder   $loggerBuilder
     */
    public function __construct(FileCacheReader $reader, FileCacheWriter $writer, LoggerBuilder $loggerBuilder)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->logger = $loggerBuilder->omitRequestData()->changeNamespace('file-cache')->build();
    }
    
    
    /**
     * @param array $allowedClasses
     */
    public function setDeserializeWhitelist(array $allowedClasses): void
    {
        $this->allowedClasses = $allowedClasses;
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @return bool
     */
    public function cacheFileExists(string $cacheNamespace, string $cacheKey): bool
    {
        return $this->reader->cacheFileExists($cacheNamespace, $cacheKey);
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @return CachedData
     *
     * @throws InvalidCacheKeyException
     */
    public function getCachedData(string $cacheNamespace, string $cacheKey): CachedData
    {
        $cachedDataJson = $this->reader->getCacheFileContent($cacheNamespace, $cacheKey);
        
        return CachedData::createFromJson($cachedDataJson, $this->allowedClasses);
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     * @param string $content
     *
     * @return bool
     */
    public function createCacheFile(string $cacheNamespace, string $cacheKey, string $content): bool
    {
        try {
            $this->writer->createCacheFile($cacheNamespace, $cacheKey, $content);
        } catch (Exceptions\FileCreationFailed $e) {
            $this->logger->error($e->getMessage());
            
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @return bool
     */
    public function deleteCacheFile(string $cacheNamespace, string $cacheKey): bool
    {
        try {
            $this->writer->deleteCacheFile($cacheNamespace, $cacheKey);
        } catch (Exceptions\FileDeletionFailed $e) {
            $this->logger->error($e->getMessage());
            
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param string $cacheNamespace
     *
     * @return bool
     */
    public function deleteAllCacheFilesByNamespace(string $cacheNamespace): bool
    {
        try {
            $this->writer->deleteAllCacheFilesByNamespace($cacheNamespace);
        } catch (Exceptions\FileDeletionFailed $e) {
            $this->logger->error($e->getMessage());
            
            return false;
        }
        
        return true;
    }
}