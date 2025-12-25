<?php

/* --------------------------------------------------------------
  AdminAccessGroupItemCollection.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class AdminAccessGroupItemCollection
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Collections
 */
class AdminAccessGroupItemCollection extends AbstractCollection
{
    /**
     * Return this collections valid type.
     *
     * @return string Valid type for group collection.
     */
    protected function _getValidType()
    {
        return AdminAccessGroupItemInterface::class;
    }
    
    
    /**
     * Add a new group item to this collection.
     *
     * @param \AdminAccessGroupItemInterface $item Item which should add to the collection
     *
     * @return \AdminAccessGroupItemCollection
     *
     * @throws \InvalidArgumentException When $item has an invalid type.
     */
    public function add(AdminAccessGroupItemInterface $item)
    {
        $this->_add($item);
        
        return $this;
    }
    
    
    /**
     * Removes a given group item from this collection.
     *
     * @param AdminAccessGroupItemInterface $item Group item.
     *
     * @return AdminAccessGroupitemCollection Returns same instance for chained method calls.
     *
     * @throws GroupItemNotFoundInCollectionException
     */
    public function remove(AdminAccessGroupItemInterface $item)
    {
        $index = false;
        if (count($this->collectionContentArray) > 0) {
            /** @var \AdminAccessGroupItemInterface $collectionItem */
            foreach ($this->collectionContentArray as $key => $collectionItem) {
                if ($item->getGroupId() === $collectionItem->getGroupId()
                    && $item->getIdentifier() === $collectionItem->getIdentifier()
                    && $item->getType() === $collectionItem->getType()) {
                    $index = $key;
                    break;
                }
            }
        }
        
        if (false === $index) {
            throw new GroupItemNotFoundInCollectionException(new NonEmptyStringType('Could not remove group item because it does not exist in collection.'));
        }
        
        unset($this->collectionContentArray[$index]);
        
        return $this;
    }
}
