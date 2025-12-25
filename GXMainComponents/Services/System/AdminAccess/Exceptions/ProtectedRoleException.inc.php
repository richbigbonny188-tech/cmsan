<?php

/* --------------------------------------------------------------
   ProtectedRoleException.inc.php 2018-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProtectedRoleException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class ProtectedRoleException extends Exception
{
    /**
     * ProtectedRoleException constructor.
     *
     * @param IdType $roleId Role ID.
     */
    public function __construct(IdType $roleId)
    {
        parent::__construct('Protected access role: ' . $roleId->asInt() . ' can not be updated or deleted.');
    }
}
