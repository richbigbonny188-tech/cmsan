<?php

/* --------------------------------------------------------------
   RoleNotFoundException.inc.php 2017-10-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class RoleNotFoundException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class RoleNotFoundException extends Exception
{
    /**
     * RoleNotFoundException constructor.
     *
     * @param IdType $roleId Role ID.
     */
    public function __construct(IdType $roleId)
    {
        parent::__construct('Access Role with id: ' . $roleId->asInt() . ' not found.');
    }
}
