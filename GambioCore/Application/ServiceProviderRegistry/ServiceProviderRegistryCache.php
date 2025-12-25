<?php
/* --------------------------------------------------------------
 ServiceProviderRegistry.php 2021-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ServiceProviderRegistry;

use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\SafeCache;

/**
 * Class ServiceProviderRegistryCache
 *
 * @package Gambio\Core\Application\ServiceProviderRegistry
 * @deprecated In favor of `Gambio\Core\Application\ModuleRegistry\ServiceProviderLoader`
 */
class ServiceProviderRegistryCache
{
    private const CACHE_KEY = 'service_provider_registry';
    
    /**
     * @var SafeCache
     */
    private $cache;
    
    
    /**
     * ServiceProviderRegistryCache constructor.
     *
     * @param CacheFactory $cacheFactory
     */
    public function __construct(CacheFactory $cacheFactory)
    {
        $this->cache = $cacheFactory->createCacheFor(self::CACHE_KEY);
    }
    
    
    /**
     * Checks if service provider registry cache exists.
     *
     * @param ServiceProviderRegistryType $type
     *
     * @return bool
     */
    public function hasRegistry(ServiceProviderRegistryType $type): bool
    {
        return $this->cache->has($type->type());
    }
    
    
    /**
     * Returns the service provider registry cache.
     *
     * @param ServiceProviderRegistryType $type
     *
     * @return array
     */
    public function getRegistry(ServiceProviderRegistryType $type): array
    {
        return $this->cache->get($type->type(), []);
    }
    
    
    /**
     * Updates the service provider registry cache.
     *
     * @param ServiceProviderRegistryType $type
     * @param array                       $registryData
     */
    public function setRegistry(ServiceProviderRegistryType $type, array $registryData): void
    {
        $this->cache->set($type->type(), $registryData);
    }
}