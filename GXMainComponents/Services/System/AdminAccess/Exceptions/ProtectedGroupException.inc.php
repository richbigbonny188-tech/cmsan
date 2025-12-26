<?php

/* --------------------------------------------------------------
   ProtectedGroupException.inc.php 2018-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProtectedGroupException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class ProtectedGroupException extends Exception
{
    /**
     * ProtectedGroupException constructor.
     *
     * @param IdType $groupId Group ID.
     */
    public function __construct(IdType $groupId)
    {
        parent::__construct('Protected access group: ' . $groupId->asInt() . ' can not be updated or deleted.');
    }
}
