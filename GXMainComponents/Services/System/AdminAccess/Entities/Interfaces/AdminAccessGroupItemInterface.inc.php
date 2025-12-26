<?php

/* --------------------------------------------------------------
   AdminAccessGroupItemInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessGroupItemInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
interface AdminAccessGroupItemInterface
{
    /**
     * Returns the group id.
     *
     * @return int Group id.
     */
    public function __construct(IntType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type);
    
    
    /**
     * Returns the group id.
     *
     * @return int Group id.
     */
    public function getGroupId();
    
    
    /**
     * Returns the group item identifier.
     *
     * @return string Group item identifier.
     */
    public function getIdentifier();
    
    
    /**
     * Returns the group item type.
     *
     * @return string Group item type.
     */
    public function getType();
    
    
    /**
     * Sets the group id.
     *
     * @param int Group id.
     *
     * @return AdminAccessGroupItemInterface
     */
    public function setGroupId(IntType $value);
    
    
    /**
     * Sets the group item identifier.
     *
     * @param NonEmptyStringType Group item identifier.
     *
     * @return AdminAccessGroupItemInterface
     */
    public function setIdentifier(NonEmptyStringType $value);
    
    
    /**
     * Sets the group item type.
     *
     * @param NonEmptyStringType Group item type.
     *
     * @return AdminAccessGroupItemInterface
     */
    public function setType(NonEmptyStringType $value);
}
