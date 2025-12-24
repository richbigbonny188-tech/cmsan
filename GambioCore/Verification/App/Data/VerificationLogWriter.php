<?php
/*--------------------------------------------------------------
   VerificationLogWriter.php 2023-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Verification\App\Data;

use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\SafeCache;
use Psr\Log\LoggerInterface;
use function Gambio\Core\Logging\logger;

/**
 * Class VerificationLogWriter
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\App\Data
 * @codeCoverageIgnore
 */
class VerificationLogWriter
{
    private const CACHE_TTL_HOURS = 24;
    
    /**
     * VerificationLogWriter constructor.
     *
     * @param CacheFactory $cacheFactory
     */
    public function __construct(private CacheFactory $cacheFactory) { }
    
    
    /**
     * @param string $message
     * @param array  $context
     * @param string $module
     *
     * @return void
     */
    public function error(string $message, array $context, string $module): void
    {
        $message = strip_tags($message);
        
        if ($this->wasRecentlyLogged($message, $module)) {
            
            return;
        }
        
        $this->logger($module)->error($message, $context);
        $this->addMessageToCache($message, $module);
    }
    
    
    /**
     * @param string $filename
     *
     * @return LoggerInterface
     */
    private function logger(string $filename): LoggerInterface
    {
        return logger($filename);
    }
    
    
    /**
     * @param string $message
     * @param string $module
     *
     * @return bool
     */
    private function wasRecentlyLogged(string $message, string $module): bool
    {
        return $this->moduleCache($module)->has(sha1($message));
    }
    
    
    /**
     * @param string $message
     * @param string $module
     *
     * @return void
     */
    private function addMessageToCache(string $message, string $module): void
    {
        $this->moduleCache($module)->set(sha1($message), 1, static::CACHE_TTL_HOURS * 60 * 60);
    }
    
    
    /**
     * @param string $module
     *
     * @return SafeCache
     */
    private function moduleCache(string $module): SafeCache
    {
        return $this->cacheFactory->createCacheFor($module);
    }
}