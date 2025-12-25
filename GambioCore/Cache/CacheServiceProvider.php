<?php
/* --------------------------------------------------------------
 CacheFactoryServiceProvider.php 2021-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Cache;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Cache\App\ClearFileCacheService;
use Gambio\Core\Cache\App\Data\FileCacheReader;
use Gambio\Core\Cache\App\Data\FileCacheRepository;
use Gambio\Core\Cache\App\Data\FileCacheWriter;
use Gambio\Core\Cache\App\FileCacheFactory;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\CacheInitializerAggregate;
use Gambio\Core\Cache\Services\ClearCacheService;
use Gambio\Core\Logging\LoggerBuilder;

/**
 * Class CacheFactoryServiceProvider
 * @package Gambio\Core\Cache
 */
class CacheServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CacheFactory::class,
            ClearCacheService::class,
            CacheInitializerAggregate::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(FileCacheReader::class)->addArgument(__DIR__ . '/../../cache');
        
        $this->application->registerShared(FileCacheWriter::class)->addArgument(__DIR__ . '/../../cache');
        
        $this->application->registerShared(FileCacheRepository::class)
            ->addArgument(FileCacheReader::class)
            ->addArgument(FileCacheWriter::class)
            ->addArgument(LoggerBuilder::class);
        
        $this->application->registerShared(CacheFactory::class, FileCacheFactory::class)
            ->addArgument(FileCacheRepository::class)
            ->addArgument(CacheInitializerAggregate::class);
        
        $this->application->registerShared(ClearCacheService::class, ClearFileCacheService::class)
            ->addArgument(CacheFactory::class);
        
        $this->application->registerShared(CacheInitializerAggregate::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(ClearCacheService::class)
            ->invokeMethod('addNamespaceToModuleCaches', ['service_provider_registry']);
    }
}