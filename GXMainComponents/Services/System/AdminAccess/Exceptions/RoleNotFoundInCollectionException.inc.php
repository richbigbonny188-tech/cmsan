<?php

/* --------------------------------------------------------------
   RoleNotFoundInCollectionException.inc.php 2017-10-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class RoleNotFoundInCollectionException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class RoleNotFoundInCollectionException extends Exception
{
    
    /**
     * RoleNotFoundInCollectionException constructor.
     *
     * @param NonEmptyStringType $message Message that explains why exception occurred.
     */
    public function __construct(NonEmptyStringType $message)
    {
        parent::__construct($message->asString());
    }
}
