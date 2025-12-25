<?php
/* --------------------------------------------------------------
   UserConfigurationNotFoundException.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\App\Exceptions;

use Exception;

/**
 * Class UserConfigurationNotFound
 *
 * @package Gambio\Core\UserConfiguration\App\Exceptions
 * @codeCoverageIgnore
 */
class UserConfigurationNotFound extends Exception
{
    /**
     * @param int    $userId
     * @param string $key
     *
     * @return UserConfigurationNotFound
     */
    public static function for(int $userId, string $key): UserConfigurationNotFound
    {
        return new self('No user configuration found for user "' . $userId . '" and key "' . $key . '".');
    }
}