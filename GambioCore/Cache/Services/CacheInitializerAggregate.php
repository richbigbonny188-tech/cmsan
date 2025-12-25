<?php
/* --------------------------------------------------------------
   CacheInitializerAggregate.php 2020-11-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\Services;

use Gambio\Core\Cache\App\CacheInitializer;
use Gambio\Core\Cache\Model\CachedData;

/**
 * Class CacheInitializerAggregate
 *
 * @package Gambio\Core\Cache\Services
 */
class CacheInitializerAggregate
{
    /**
     * @var array<string, CacheInitializer>
     */
    private $initializers = [];
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @return bool
     */
    public function hasInitializerFor(string $cacheNamespace, string $cacheKey): bool
    {
        return array_key_exists($cacheNamespace, $this->initializers)
               && $this->initializers[$cacheNamespace]->hasInitialValueForKey($cacheKey);
    }
    
    
    /**
     * @param string $cacheNamespace
     * @param string $cacheKey
     *
     * @return CachedData
     */
    public function getInitialValueFor(string $cacheNamespace, string $cacheKey): CachedData
    {
        return $this->initializers[$cacheNamespace]->getInitialValueForKey($cacheKey);
    }
    
    
    /**
     * @param string           $cacheNamespace
     * @param CacheInitializer $initializer
     */
    public function setInitializerForCache(string $cacheNamespace, CacheInitializer $initializer): void
    {
        $this->initializers[$cacheNamespace] = $initializer;
    }
}