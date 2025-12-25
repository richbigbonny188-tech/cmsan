<?php
/* --------------------------------------------------------------
   FileDeletionFailed.php 2020-11-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\App\Data\Exceptions;

use Exception;

/**
 * Class FileDeletionFailed
 *
 * @package Gambio\Core\Cache\App\Data\Exceptions
 */
class FileDeletionFailed extends Exception
{
    /**
     * @param string $cacheFile
     *
     * @return FileDeletionFailed
     */
    public static function forCacheFile(string $cacheFile): FileDeletionFailed
    {
        return new self('Could not delete cache file "' . $cacheFile . '".');
    }
}