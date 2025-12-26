<?php
/* --------------------------------------------------------------
   AccessRoleDoesNotExistException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Services\Exceptions;

use Exception;

/**
 * Class AccessRoleDoesNotExistException
 *
 * @package Gambio\Admin\Modules\AccessRole\Services\Exceptions
 * @codeCoverageIgnore
 */
class AccessRoleDoesNotExistException extends Exception
{
    /**
     * @param int $id
     *
     * @return AccessRoleDoesNotExistException
     */
    public static function forId(int $id): AccessRoleDoesNotExistException
    {
        return new self('Access role with ID ' . $id . ' does not exist.');
    }
}