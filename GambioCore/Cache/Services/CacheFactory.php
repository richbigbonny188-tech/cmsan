<?php
/* --------------------------------------------------------------
   CacheFactory.php 2020-11-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\Services;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Interface CacheFactory
 *
 * @package Gambio\Core\Cache\Services
 */
interface CacheFactory
{
    /**
     * Creates an exception handling PSR based cache based on the given namespace.
     *
     * @param string $namespace
     *
     * @return SafeCache
     */
    public function createCacheFor(string $namespace): SafeCache;
    
    
    /**
     * Creates an exception throwing PSR based cache based on the given namespace.
     *
     * @param string $namespace
     *
     * @return CacheInterface
     *
     * @throws InvalidArgumentException
     */
    public function createPsrCacheFor(string $namespace): CacheInterface;
    
    
    /**
     * Allows the internal deserialization of a PHP class.
     *
     * Note: The created caches use the internal PHP methods of (de)serialization.
     *       To take care of any `unserialize()` exploit, it's needed to provide a list of allowed classes.
     *
     * @param string ...$classes
     */
    public function allowDeserializationOf(string ...$classes): void;
}