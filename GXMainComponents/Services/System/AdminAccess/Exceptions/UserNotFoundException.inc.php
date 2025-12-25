<?php

/* --------------------------------------------------------------
   UserNotFoundException.inc.php 2017-10-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UserNotFoundException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class UserNotFoundException extends Exception
{
    /**
     * UserNotFoundException constructor.
     *
     * @param IdType $userId User ID.
     */
    public function __construct(IdType $userId)
    {
        parent::__construct('Access User with id: ' . $userId->asInt() . ' not found.');
    }
}
