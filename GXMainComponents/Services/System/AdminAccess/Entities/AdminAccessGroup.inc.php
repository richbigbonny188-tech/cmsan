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
 * Class AdminAccessGroup
 *
 * A Group is a collection pages/controllers,
 * that represent a semantic unit for the administration of our shop system.
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
class AdminAccessGroup implements AdminAccessGroupInterface
{
    /**
     * @var AdminAccessGroupReaderInterface
     */
    
    protected $reader;
    
    /**
     * @var AdminAccessGroupWriterInterface
     */
    
    protected $writer;
    
    /**
     * @var AdminAccessGroupDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var AdminAccessGroupInterface|null
     */
    protected $parentGroup;
    
    /**
     * @var KeyValueCollection
     */
    protected $name;
    
    /**
     * @var KeyValueCollection
     */
    protected $description;
    
    /**
     * @var int
     */
    protected $sortOrder;
    
    /**
     * @var AdminAccessGroupItemCollection
     */
    protected $items;
    
    /**
     * @var bool
     */
    protected $protected;
    
    
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
    ) {
        $this->reader  = $groupReader;
        $this->writer  = $groupWriter;
        $this->deleter = $groupDeleter;
        
        $this->id          = 0;
        $this->name        = new KeyValueCollection([]);
        $this->description = new KeyValueCollection([]);
        $this->sortOrder   = 0;
        $this->parentGroup = null;
        $this->items       = $items;
    }
    
    
    /**
     * Returns the group id.
     *
     * @return int Group ID.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Returns the group parent id.
     *
     * @return int Group parent ID.
     */
    public function getParentId()
    {
        return $this->parentId;
    }
    
    
    /**
     * Returns the group descriptions as a collection.
     *
     * @return KeyValueCollection Group description in all available languages.
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    
    /**
     * Returns the group items as a collection.
     *
     * @return AdminAccessGroupItemCollection Group items.
     */
    public function getItems()
    {
        return $this->items;
    }
    
    
    /**
     * Sets the group id.
     *
     * @param IdType $id Group ID.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Sets the parent group.
     *
     * @param AdminAccessGroupInterface|null $parentGroup Parent group.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setParentGroup(?AdminAccessGroupInterface $parentGroup)
    {
        $this->parentGroup = $parentGroup;
        
        return $this;
    }
    
    
    /**
     * Returns the group names as a collection.
     *
     * @return KeyValueCollection Group name in all available languages.
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Sets the group sort order.
     *
     * @param IntType $sortOrder Group sort order.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setSortOrder(IntType $sortOrder)
    {
        $this->sortOrder = $sortOrder->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the group sort order.
     *
     * @return int Group sort order.
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Sets the group names.
     *
     * @param KeyValueCollection $name Group name.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setName(KeyValueCollection $name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    
    /**
     * Sets the group descriptions.
     *
     * @param KeyValueCollection $description Group description.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setDescription(KeyValueCollection $description)
    {
        $this->description = $description;
        
        return $this;
    }
    
    
    /**
     * Sets the group items.
     *
     * @param AdminAccessGroupItemCollection Group items.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setItems(AdminAccessGroupItemCollection $items)
    {
        $this->items = $items;
        
        return $this;
    }
    
    
    /**
     * Returns this groups children.
     *
     * @return AdminAccessGroupCollection.
     */
    public function getChildren()
    {
        return $this->reader->getChildren(new IdType($this->id));
    }
    
    
    /**
     * Returns this groups parent group.
     *
     * @return AdminAccessGroupInterface.
     *
     * @throws GroupNotFoundException
     */
    public function getParentGroup()
    {
        if ($this->parentGroup === null) {
            throw new GroupNotFoundException(new NonEmptyStringType('[parent of group with id ' . $this->id . ']'));
        }
        
        return $this->parentGroup;
    }
    
    
    /**
     * Adds an item to the group.
     *
     * @param AdminAccessGroupItemInterface $item Group item.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function addItem(AdminAccessGroupItemInterface $item)
    {
        $this->items->add($item);
        
        return $this;
    }
    
    
    /**
     * Removes an item from the group.
     *
     * @param AdminAccessGroupItemInterface $item Group item.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     *
     * @throws GroupItemNotFoundInCollectionException
     */
    public function removeItem(AdminAccessGroupItemInterface $item)
    {
        $this->items->remove($item);
        
        return $this;
    }
    
    
    /**
     * Deletes an access group.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     *
     * @throws ProtectedGroupException
     */
    public function delete()
    {
        if ($this->protected) {
            throw new ProtectedGroupException(new IdType($this->id));
        }
        
        $this->deleter->delete(new IdType($this->id));
        
        return $this;
    }
    
    
    /**
     * Stores this group into the database.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     *
     * @throws ProtectedGroupException
     */
    public function store()
    {
        if ($this->protected) {
            throw new ProtectedGroupException(new IdType($this->id));
        }
        
        if ($this->id > 0) {
            $this->update();
        }
        
        $this->id = $this->writer->insert($this);
        
        return $this;
    }
    
    
    /**
     * Updates this group in the database.
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     *
     * @throws ProtectedGroupException
     */
    public function update()
    {
        if ($this->protected) {
            throw new ProtectedGroupException(new IdType($this->id));
        }
        
        $this->writer->update($this);
        
        return $this;
    }
    
    
    /**
     * Sets the protected value.
     *
     * @param BoolType $value
     *
     * @return AdminAccessGroupInterface Returns same instance for chained method calls.
     */
    public function setProtected(BoolType $value)
    {
        $this->protected = $value->asBool();
        
        return $this;
    }
    
    
    /**
     * Returns the protected value.
     *
     * @return bool
     */
    public function getProtected()
    {
        return $this->protected;
    }
}
