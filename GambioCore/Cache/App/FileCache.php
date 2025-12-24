<?php
/* --------------------------------------------------------------
   FileCache.php 2023-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\App;

use Gambio\Core\Cache\App\Data\FileCacheRepository;
use Gambio\Core\Cache\Model\CachedData;
use Gambio\Core\Cache\Services\CacheInitializerAggregate;
use Gambio\Core\Cache\Services\Exceptions\InvalidCacheKeyException;
use Gambio\Core\Cache\Services\Exceptions\InvalidCacheNamespaceException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class FileCache
 *
 * @package Gambio\Core\Cache\App
 */
class FileCache implements CacheInterface
{
    /**
     * @var FileCacheRepository
     */
    private $fileCacheRepository;
    
    /**
     * @var CacheInitializerAggregate
     */
    private $cacheInitializer;
    
    /**
     * @var string
     */
    private $namespace;
    
    /**
     * @var CachedData[]
     */
    private $cachedData;
    
    
    /**
     * FileCache constructor.
     *
     * @param FileCacheRepository       $fileCacheRepository
     * @param CacheInitializerAggregate $cacheInitializer
     * @param string                    $namespace
     */
    protected function __construct(
        FileCacheRepository $fileCacheRepository,
        CacheInitializerAggregate $cacheInitializer,
        string $namespace
    ) {
        $this->fileCacheRepository = $fileCacheRepository;
        $this->cacheInitializer    = $cacheInitializer;
        $this->namespace           = $namespace;
        $this->cachedData          = [];
    }
    
    
    /**
     * @param FileCacheRepository       $fileCacheRepository
     * @param CacheInitializerAggregate $cacheInitializer
     * @param string                    $namespace
     *
     * @return static
     *
     * @throws InvalidCacheNamespaceException
     */
    public static function create(
        FileCacheRepository $fileCacheRepository,
        CacheInitializerAggregate $cacheInitializer,
        string $namespace
    ) {
        if (strlen($namespace) > 64 || preg_match('/^[A-Za-z0-9_\.]+$/', $namespace) !== 1) {
            throw InvalidCacheNamespaceException::forString($namespace);
        }
        
        return new static($fileCacheRepository, $cacheInitializer, $namespace);
    }
    
    
    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if ($this->validateKey($key) === false) {
            throw InvalidCacheKeyException::forString($key);
        }
        
        if ($this->has($key) === false) {
            return $default;
        }
        
        return $this->cachedData[$key]->cachedValue();
    }
    
    
    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        $this->cachedData[$key] = CachedData::create($key, $value, ($ttl === null) ? null : time() + $ttl);
        
        return $this->fileCacheRepository->createCacheFile($this->namespace, $key, (string)$this->cachedData[$key]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        if ($this->validateKey($key) === false) {
            throw InvalidCacheKeyException::forString($key);
        }
        
        return $this->fileCacheRepository->deleteCacheFile($this->namespace, $key);
    }
    
    
    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return $this->fileCacheRepository->deleteAllCacheFilesByNamespace($this->namespace);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        
        return $result;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $success = 1;
        foreach ($values as $key => $value) {
            $result  = $this->set($key, $value, $ttl);
            $success &= $result;
        }
        
        return (bool)$success; // casting to bool because an int could be returned otherwise
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        $success = 1;
        foreach ($keys as $key) {
            $success &= $this->delete($key);
        }
        
        return (bool)$success; // casting to bool because an int could be returned otherwise
    }
    
    
    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        if ($this->validateKey($key) === false) {
            throw InvalidCacheKeyException::forString($key);
        }
        
        if (!isset($this->cachedData[$key])) {
            if ($this->fileCacheRepository->cacheFileExists($this->namespace, $key) === false) {
                return $this->buildCachedDataIfPossible($key);
            }
            
            $this->cachedData[$key] = $this->fileCacheRepository->getCachedData($this->namespace, $key);
        }
        
        if ($this->cachedData[$key]->isExpired()) {
            $this->delete($key);
            
            return $this->buildCachedDataIfPossible($key);
        }
        
        return true;
    }
    
    
    /**
     * @param string $key
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    private function buildCachedDataIfPossible(string $key): bool
    {
        if ($this->cacheInitializer->hasInitializerFor($this->namespace, $key)) {
            $this->cachedData[$key] = $this->cacheInitializer->getInitialValueFor($this->namespace, $key);
            $this->set($key, $this->cachedData[$key]->cachedValue(), $this->cachedData[$key]->expirationTimestamp());
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * @param string $key
     *
     * @return bool
     */
    private function validateKey(string $key): bool
    {
        return preg_match('/^[A-Za-z0-9_\.]+$/', $key) === 1 && strlen($key) <= 64;
    }
}