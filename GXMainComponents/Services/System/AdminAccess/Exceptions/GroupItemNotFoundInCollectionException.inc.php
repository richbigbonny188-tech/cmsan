<?php

/* --------------------------------------------------------------
   GroupItemNotFoundInCollectionException.inc.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GroupItemNotFoundInCollectionException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class GroupItemNotFoundInCollectionException extends Exception
{
    
    /**
     * GroupItemNotFoundInCollectionException constructor.
     *
     * @param NonEmptyStringType $message Message that explains why exception occurred.
     */
    public function __construct(NonEmptyStringType $message)
    {
        parent::__construct($message->asString());
    }
}
