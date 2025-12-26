<?php
/* --------------------------------------------------------------
   StorageOfAdminsFailedException.php 2021-04-07
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
 * Class StorageOfAdminsFailedException
 *
 * @package Gambio\Admin\Modules\Admin\Services\Exceptions
 * @codeCoverageIgnore
 */
class StorageOfAdminsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfAdminsFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfAdminsFailedException
    {
        return new self('Could not store admins because of previous error.', 0, $exception);
    }
}