<?php
/* --------------------------------------------------------------
   SafeFileCache.php 2020-11-30
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
use Gambio\Core\Cache\Services\CacheInitializerAggregate;
use Gambio\Core\Cache\Services\SafeCache;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class SafeFileCache
 *
 * @package Gambio\Core\Cache\App
 */
class SafeFileCache extends FileCache implements SafeCache
{
    /**
     * @param FileCacheRepository       $fileCacheRepository
     * @param CacheInitializerAggregate $cacheInitializer
     * @param string                    $namespace
     *
     * @return static
     */
    public static function create(
        FileCacheRepository $fileCacheRepository,
        CacheInitializerAggregate $cacheInitializer,
        string $namespace
    ) {
        $namespace = preg_replace('/[^A-Za-z0-9_\.]/', '', $namespace);
        
        return new static($fileCacheRepository,
                          $cacheInitializer,
                          (strlen($namespace) > 64) ? substr($namespace, 0, 64) : $namespace);
    }
    
    
    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        try {
            return parent::get($key, $default);
        } catch (InvalidArgumentException $e) {
            return $default;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        try {
            return parent::set($key, $value, $ttl);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        try {
            return parent::delete($key);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        try {
            return parent::has($key);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}