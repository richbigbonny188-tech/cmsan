<?php
/*--------------------------------------------------------------
   TransmissionCache.php 2023-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App\Data;

use Exception;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\SafeCache;

/**
 * Class TransmissionCache
 *
 * @package Gambio\Core\ErrorHandling\App\Data
 */
class TransmissionCache
{
    private const CACHE_TTL_HOURS = 24;
    private const CACHE_KEY       = 'ExceptionTransmission';
    private SafeCache $cache;
    
    
    /**
     * TransmissionCache constructor.
     *
     * @param CacheFactory $cacheFactory
     */
    public function __construct(CacheFactory $cacheFactory)
    {
        $this->cache = $cacheFactory->createCacheFor(static::CACHE_KEY);
    }
    
    
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function wasRecentlyTransmitted(Exception $exception): bool
    {
        return $this->cache->has($this->generateCacheKey($exception));
    }
    
    
    /**
     * @param Exception $exception
     *
     * @return void
     */
    public function add(Exception $exception): void
    {
        $this->cache->set($this->generateCacheKey($exception), 1, static::CACHE_TTL_HOURS * 60 * 60);
    }
    
    
    /**
     * @param Exception $exception
     *
     * @return string
     */
    private function generateCacheKey(Exception $exception): string
    {
        return sha1($exception->getMessage());
    }
}