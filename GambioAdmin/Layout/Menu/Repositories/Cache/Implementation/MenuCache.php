<?php
/* --------------------------------------------------------------
 MenuCache.php 2020-11-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Repositories\Cache\Implementation;

use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuGroups;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Cache\Services\CacheFactory;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use function Gambio\Core\Logging\logger;

/**
 * Class MenuCache
 * @package Gambio\Admin\Layout\Menu\Repositories\Cache
 */
class MenuCache
{
    private const CACHE_NAME = 'admin_menu';
    
    /**
     * @var CacheInterface
     */
    private $cache;
    
    /**
     * @var string
     */
    private $cacheName;
    
    
    /**
     * MenuCache constructor.
     *
     * @param CacheFactory    $cacheFactory
     * @param UserPreferences $userPreferences
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        CacheFactory $cacheFactory,
        UserPreferences $userPreferences
    ) {
        $this->cacheName = $this->getCacheName($userPreferences->languageId());
        $this->cache     = $cacheFactory->createPsrCacheFor(self::CACHE_NAME);
    }
    
    
    /**
     * Checks if admin menu cache is available.
     *
     * @return bool
     */
    public function hasCache(): bool
    {
        try {
            return $this->cache->has($this->cacheName);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
    
    
    /**
     * Provides menu data from cache.
     * Returns an empty array and logs related errors if something fails.
     *
     * @return array
     */
    public function getCache(): array
    {
        try {
            return $this->cache->get($this->cacheName, []);
        } catch (InvalidArgumentException $e) {
            logger()->error('Failed to get cache', ['exception' => $e]);
            
            return [];
        }
    }
    
    
    /**
     * Deletes the menu cache for the given language.
     *
     * @param int $languageId
     */
    public function deleteCache(int $languageId): void
    {
        try {
            $this->cache->delete($this->getCacheName($languageId));
        } catch (InvalidArgumentException $e) {
            logger()->error('Failed to delete cache', ['exception' => $e]);
        }
    }
    
    
    /**
     * Caches menu data.
     *
     * This function serializes the menu groups aggregate and writes the data to
     * the admin_menu cache.
     *
     * @param MenuGroups $groups
     */
    public function buildMenuCache(MenuGroups $groups): void
    {
        try {
            $this->cache->set($this->cacheName, $groups->toArray());
        } catch (InvalidArgumentException $e) {
            logger()->error('Failed to store cache', ['exception' => $e]);
        }
    }
    
    
    /**
     * Provides the cache name.
     *
     * @param int $languageId
     *
     * @return string
     */
    private function getCacheName(int $languageId): string
    {
        return self::CACHE_NAME . "_{$languageId}";
    }
}