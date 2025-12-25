<?php

/* --------------------------------------------------------------
  AdminAccessGroupCollection.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class AdminAccessGroupCollection
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Collections
 */
class AdminAccessGroupCollection extends AbstractCollection
{
    /**
     * Returns a group object from this collection by a given group id.
     *
     * @param IdType $id ID of group that collection must return.
     *
     * @return AdminAccessGroup Group object from collection.
     * @throws GroupNotFoundInCollectionException If group object not found in collection.
     *
     */
    public function getById(IdType $id)
    {
        /** @var AdminAccessGroup $group */
        foreach ($this->collectionContentArray as $group) {
            if ($group->getId() === $id->asInt()) {
                return $group;
            }
        }
        
        throw new GroupNotFoundInCollectionException($id);
    }
    
    
    /**
     * Return this collections valid type.
     *
     * @return string Valid type for group collection.
     */
    protected function _getValidType()
    {
        return AdminAccessGroupInterface::class;
    }
}
