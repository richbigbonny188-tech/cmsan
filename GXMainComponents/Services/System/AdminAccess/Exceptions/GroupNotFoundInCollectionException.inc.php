<?php

/* --------------------------------------------------------------
   GroupNotFoundInCollectionException.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GroupNotFoundInCollectionException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class GroupNotFoundInCollectionException extends Exception
{
    
    /**
     * GroupNotFoundInCollectionException constructor.
     *
     * @param IdType $id ID of Group object that was not found in the group collection.
     */
    public function __construct(IdType $id)
    {
        parent::__construct("Group with id: $id was not found in the group collection.");
    }
}
