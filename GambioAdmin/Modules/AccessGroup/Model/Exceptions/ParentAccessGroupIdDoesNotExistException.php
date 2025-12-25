<?php
/* --------------------------------------------------------------
   ParentAccessGroupIdDoesNotExistException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\Exceptions;

use Exception;

/**
 * Class ParentAccessGroupIdDoesNotExistException
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Exceptions
 * @codeCoverageIgnore
 */
class ParentAccessGroupIdDoesNotExistException extends Exception
{
    /**
     * @param int $id
     *
     * @return ParentAccessGroupIdDoesNotExistException
     */
    public static function forGroup(int $id): ParentAccessGroupIdDoesNotExistException
    {
        return new self('There is no parent group for the group with ID ' . $id . ' defined.');
    }
}