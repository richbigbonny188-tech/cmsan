<?php
/* --------------------------------------------------------------
   CacheInitializer.php 2020-11-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\App;

use Gambio\Core\Cache\Model\CachedData;

/**
 * Interface CacheInitializer
 *
 * @package Gambio\Core\Cache\App
 */
interface CacheInitializer
{
    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasInitialValueForKey(string $key): bool;
    
    
    /**
     * @param string $key
     *
     * @return CachedData
     */
    public function getInitialValueForKey(string $key): CachedData;
}