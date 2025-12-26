<?php

/* --------------------------------------------------------------
   AdminAccessGroupManagerInterface.inc.php 2018-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessGroupManagerInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Managers
 */
interface AdminAccessGroupManagerInterface
{
    /**
     * Returns all groups as a colleczion.
     *
     * @return AdminAccessGroupCollection Group collection.
     */
    public function getAllGroups();
    
    
    /**
     * Returns a group by a given controller identifier.
     *
     * @param \NonEmptyStringType $identifier Controller identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByController(NonEmptyStringType $identifier);
    
    
    /**
     * Returns a group by a given page identifier.
     *
     * @param \NonEmptyStringType $identifier Page identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByPage(NonEmptyStringType $identifier);
    
    
    /**
     * Returns a group by a given ajax handler identifier.
     *
     * @param \NonEmptyStringType $identifier Ajax handler identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByAjaxHandler(NonEmptyStringType $identifier);
    
    
    /**
     * Returns a group by a given group id.
     *
     * @param IdType $id Group id.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupById(IdType $id);
    
    
    /**
     * Adds a group item to an existing group.
     *
     * @param \IdType             $groupId    Id of the group.
     * @param \NonEmptyStringType $identifier Identifier for this item.
     * @param \NonEmptyStringType $type       Type of this item.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function addItemToGroup(IdType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type);
    
    
    /**
     * Removes a group item from an existing group.
     *
     * @param \IdType             $groupId    Id of the group.
     * @param \NonEmptyStringType $identifier Identifier for this item.
     * @param \NonEmptyStringType $type       Type of this item.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     * @throws \GroupItemNotFoundInCollectionException
     */
    public function removeItemFromGroup(IdType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type);
    
    
    /**
     * Creates a new group.
     *
     * @param KeyValueCollection $name        Collection with the group names. Index of a group name must be his
     *                                        language code.
     * @param KeyValueCollection $description Collection with the group descriptions. Index of a group name must be his
     *                                        language code.
     * @param IdType             $parentId    Id of the parent group.
     * @param IntType            $sortOrder   Group sorts order.
     *
     * @return AdminAccessGroupInterface Returns the create role.
     *
     * @throws \GroupNotFoundException
     */
    public function createNewGroup(
        KeyValueCollection $name,
        KeyValueCollection $description,
        IdType $parentId,
        IntType $sortOrder
    );
    
    
    /**
     * Updates a group by a given group ID.
     *
     * @param KeyValueCollection $newName        Collection with the group names. Index of a group name must be his
     *                                           language code.
     * @param KeyValueCollection $newDescription Collection with the group descriptions. Index of a group name must be
     *                                           his language code.
     * @param IdType             $newParentId    New id of the parent group.
     * @param IntType            $newSortOrder   New group sorts order.
     *
     * @return AdminAccessGroupManager Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function updateGroup(
        IdType $id,
        KeyValueCollection $newName,
        KeyValueCollection $newDescription,
        IdType $newParentId,
        IntType $newSortOrder
    );
    
    
    /**
     * Deletes a group by a given group ID.
     *
     * @param IdType $id ID of the group that should be deleted.
     *
     * @return AdminAccessGroupManager Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function deleteGroupById(IdType $id);
}
