<?php
/* --------------------------------------------------------------
   AdminDoesNotExistException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\Services\Exceptions;

use Exception;

/**
 * Class AdminDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Admin\Services\Exceptions
 * @codeCoverageIgnore
 */
class AdminDoesNotExistException extends Exception
{
    /**
     * @param int $id
     *
     * @return AdminDoesNotExistException
     */
    public static function forId(int $id): AdminDoesNotExistException
    {
        return new self('Admin with ID ' . $id . ' does not exist.');
    }
}