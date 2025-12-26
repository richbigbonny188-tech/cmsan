<?php
/* --------------------------------------------------------------
 FileCacheFactory.php 2020-11-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Cache\App;

use Gambio\Core\Cache\App\Data\FileCacheRepository;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\CacheInitializerAggregate;
use Gambio\Core\Cache\Services\SafeCache;
use Psr\SimpleCache\CacheInterface;

/**
 * Class FileCacheFactory
 *
 * @package Gambio\Core\Cache\App
 */
class FileCacheFactory implements CacheFactory
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
     * @var string[]
     */
    private $allowedClasses;
    
    
    /**
     * CacheFactory constructor.
     *
     * @param FileCacheRepository       $fileCacheRepository
     * @param CacheInitializerAggregate $cacheInitializer
     */
    public function __construct(FileCacheRepository $fileCacheRepository, CacheInitializerAggregate $cacheInitializer)
    {
        $this->fileCacheRepository = $fileCacheRepository;
        $this->cacheInitializer    = $cacheInitializer;
        $this->allowedClasses      = [];
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCacheFor(string $namespace): SafeCache
    {
        $this->fileCacheRepository->setDeserializeWhitelist(array_values($this->allowedClasses));
        
        return SafeFileCache::create($this->fileCacheRepository,
                                     $this->cacheInitializer,
                                     $namespace);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createPsrCacheFor(string $namespace): CacheInterface
    {
        $this->fileCacheRepository->setDeserializeWhitelist(array_values($this->allowedClasses));
        
        return FileCache::create($this->fileCacheRepository,
                                 $this->cacheInitializer,
                                 $namespace);
    }
    
    
    /**
     * @inheritDoc
     */
    public function allowDeserializationOf(string ...$classes): void
    {
        foreach ($classes as $class) {
            $this->allowedClasses[$class] = $class;
        }
    }
}