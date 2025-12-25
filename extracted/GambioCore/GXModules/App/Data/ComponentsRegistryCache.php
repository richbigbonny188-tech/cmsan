<?php
/* --------------------------------------------------------------
   ComponentsRegistryCache.php 2020-11-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data;

use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\SafeCache;
use Gambio\Core\GXModules\Model\ComponentsRegistry;

/**
 * Class ComponentsRegistryCache
 *
 * @package Gambio\Core\GXModules\App\Data
 */
class ComponentsRegistryCache
{
    /**
     * @var SafeCache
     */
    private $cache;
    
    
    /**
     * ComponentsRegistryCache constructor.
     *
     * @param CacheFactory $cacheFactory
     */
    public function __construct(CacheFactory $cacheFactory)
    {
        $this->cache = $cacheFactory->createCacheFor('gxmodules_components');
    }
    
    
    /**
     * @param string $type
     *
     * @return bool
     */
    public function has(string $type): bool
    {
        return $this->cache->has($type);
    }
    
    
    /**
     * @param string $type
     *
     * @return ComponentsRegistry
     */
    public function get(string $type): ComponentsRegistry
    {
        return $this->cache->get($type);
    }
    
    
    /**
     * @param string             $type
     * @param ComponentsRegistry $registry
     */
    public function set(string $type, ComponentsRegistry $registry): void
    {
        $this->cache->set($type, $registry);
    }
    
    
    public function clear(): void
    {
        $this->cache->clear();
    }
}