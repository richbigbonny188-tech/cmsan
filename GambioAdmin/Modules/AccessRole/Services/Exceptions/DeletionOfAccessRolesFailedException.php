<?php
/* --------------------------------------------------------------
   DeletionOfAccessRolesFailedException.php 2021-04-07
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
 * Class DeletionOfAccessRolesFailedException
 *
 * @package Gambio\Admin\Modules\AccessRole\Services\Exceptions
 * @codeCoverageIgnore
 */
class DeletionOfAccessRolesFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfAccessRolesFailedException
     */
    public static function becauseOfException(Exception $exception): DeletionOfAccessRolesFailedException
    {
        return new self('Could not delete access roles because of previous error.', 0, $exception);
    }
}