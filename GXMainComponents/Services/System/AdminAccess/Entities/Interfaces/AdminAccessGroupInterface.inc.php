<?php

/* --------------------------------------------------------------
   AdminAccessGroup.inc.php 2021-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessGroupInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
interface AdminAccessGroupInterface
{
    /**
     * AdminAccessGroup constructor.
     *
     * @param AdminAccessGroupReaderInterface  $groupReader
     * @param AdminAccessGroupWriterInterface  $groupWriter
     * @param AdminAccessGroupDeleterInterface $groupDeleter
     * @param AdminAccessGroupItemCollection   $items
     */
    public function __construct(
        AdminAccessGroupReaderInterface  $groupReader,
        AdminAccessGroupWriterInterface  $groupWriter,
        AdminAccessGroupDeleterInterface $groupDeleter,
        AdminAccessGroupItemCollection   $items
    );
    
    
    /**
     * Returns the group id.
     *
     * @return int Group ID.
     */
    public function getId();
    
    
    /**
     * Returns the group parent id.
     *
     * @return int Group parent ID.
     */
    public function getParentId();
    
    
    /**
     * Returns the group names as a collection.
     *
     * @return KeyValueCollection Group name in all available languages.
     */
    public function getName();
    
    
    /**
     * Returns the group descriptions as a collection.
     *
     * @return KeyValueCollection Group description in all available languages.
     */
    public function getDescription();
    
    
    /**
     * Returns the group items as a collection.
     *
     * @return AdminAccessGroupItemCollection Group items.
     */
    public function getItems();
    
    
    /**
     * Sets the group id.
     *
     * @param IdType $id Group ID.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Sets the parent group.
     *
     * @param AdminAccessGroupInterface|null $parentGroup Parent group.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setParentGroup(?AdminAccessGroupInterface $parentGroup);
    
    
    /**
     * Sets the group sort order.
     *
     * @param IntType $sortOrder Group sort order.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setSortOrder(IntType $sortOrder);
    
    
    /**
     * Returns the group sort order.
     *
     * @return int Group sort order.
     */
    public function getSortOrder();
    
    
    /**
     * Sets the group names.
     *
     * @param KeyValueCollection $name Group name.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setName(KeyValueCollection $name);
    
    
    /**
     * Sets the group descriptions.
     *
     * @param KeyValueCollection $description Group description.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setDescription(KeyValueCollection $description);
    
    
    /**
     * Sets the group items.
     *
     * @param AdminAccessGroupItemCollection Group items.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setItems(AdminAccessGroupItemCollection $items);
    
    
    /**
     * Returns this groups children.
     *
     * @return AdminAccessGroupCollection.
     */
    public function getChildren();
    
    
    /**
     * Returns this groups parent group.
     *
     * @return AdminAccessGroupInterface.
     *
     * @throws GroupNotFoundException
     */
    public function getParentGroup();
    
    
    /**
     * Adds an item to the group.
     *
     * @param AdminAccessGroupItemInterface $item Group item.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function addItem(AdminAccessGroupItemInterface $item);
    
    
    /**
     * Removes an item from the group.
     *
     * @param AdminAccessGroupItemInterface $item Group item.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     *
     * @throws GroupItemNotFoundInCollectionException
     */
    public function removeItem(AdminAccessGroupItemInterface $item);
    
    
    /**
     * Deletes an access group.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function delete();
    
    
    /**
     * Stores this group into the database.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function store();
    
    
    /**
     * Updates this group in the database.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function update();
    
    
    /**
     * Sets the protected value.
     *
     * @param BoolType $value
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setProtected(BoolType $value);
    
    
    /**
     * Returns the protected value.
     *
     * @return bool
     */
    public function getProtected();
}
