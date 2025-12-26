<?php
/* --------------------------------------------------------------
   StorageOfAccessGroupsFailedException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Services\Exceptions;

use Exception;

/**
 * Class StorageOfAccessGroupsFailedException
 *
 * @package Gambio\Admin\Modules\AccessGroup\Services\Exceptions
 * @codeCoverageIgnore
 */
class StorageOfAccessGroupsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfAccessGroupsFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfAccessGroupsFailedException
    {
        return new self('Could not store access groups because of previous error.', 0, $exception);
    }
}