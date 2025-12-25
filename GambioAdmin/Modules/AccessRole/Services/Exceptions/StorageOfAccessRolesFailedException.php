<?php
/* --------------------------------------------------------------
   StorageOfAccessRolesFailedException.php 2021-04-07
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
 * Class StorageOfAccessRolesFailedException
 *
 * @package Gambio\Admin\Modules\AccessRole\Services\Exceptions
 * @codeCoverageIgnore
 */
class StorageOfAccessRolesFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfAccessRolesFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfAccessRolesFailedException
    {
        return new self('Could not store access roles because of previous error.', 0, $exception);
    }
}