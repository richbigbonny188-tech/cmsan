<?php
/* --------------------------------------------------------------
   AccessGroupDoesNotExistException.php 2021-04-07
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
 * Class AccessGroupDoesNotExistException
 *
 * @package Gambio\Admin\Modules\AccessGroup\Services\Exceptions
 * @codeCoverageIgnore
 */
class AccessGroupDoesNotExistException extends Exception
{
    /**
     * @param int $id
     *
     * @return AccessGroupDoesNotExistException
     */
    public static function forId(int $id): AccessGroupDoesNotExistException
    {
        return new self('Access group with ID ' . $id . ' does not exist.');
    }
    
    
    /**
     * @param string $descriptor
     * @param string $type
     *
     * @return AccessGroupDoesNotExistException
     */
    public static function forDescriptorAndType(string $descriptor, string $type): AccessGroupDoesNotExistException
    {
        return new self('Access group with group item "' . $descriptor . '" of type "' . $type . '" does not exist.');
    }
}