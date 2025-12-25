<?php
/* --------------------------------------------------------------
   FileCreationFailed.php 2020-11-30
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
 * Class FileCreationFailed
 *
 * @package Gambio\Core\Cache\App\Data\Exceptions
 */
class FileCreationFailed extends Exception
{
    /**
     * @param string $cacheFile
     *
     * @return FileCreationFailed
     */
    public static function forCacheFile(string $cacheFile): FileCreationFailed
    {
        return new self('Could not create new cache file "' . $cacheFile . '".');
    }
}