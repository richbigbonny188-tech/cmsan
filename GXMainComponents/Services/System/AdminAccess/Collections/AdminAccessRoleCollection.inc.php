<?php

/* --------------------------------------------------------------
  AdminAccessRoleCollection.inc.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class AdminAccessRoleCollection
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Collections
 */
class AdminAccessRoleCollection extends AbstractCollection
{
    /**
     * Adds a given role to this collection.
     *
     * @param AdminAccessRoleInterface $role Access role.
     *
     * @return AdminAccessRoleCollection Returns same instance for chained method calls.
     */
    public function add(AdminAccessRoleInterface $role)
    {
        $this->_add($role);
        
        return $this;
    }
    
    
    /**
     * Removes a given role from this collection.
     *
     * @param AdminAccessRoleInterface $role Access role.
     *
     * @return AdminAccessRoleCollection Returns same instance for chained method calls.
     * @throws RoleNotFoundInCollectionException If role is not in the collection.
     *
     */
    public function remove(AdminAccessRoleInterface $role)
    {
        $index = false;
        if (count($this->collectionContentArray) > 0) {
            /** @var \AdminAccessRoleInterface $collectionItem */
            foreach ($this->collectionContentArray as $key => $collectionItem) {
                if ($role->getId() === $collectionItem->getId()) {
                    $index = $key;
                    break;
                }
            }
        }
        
        if (false === $index) {
            throw new RoleNotFoundInCollectionException(new NonEmptyStringType('Could not remove role because it does not exist in collection.'));
        }
        
        unset($this->collectionContentArray[$index]);
        
        return $this;
    }
    
    
    /**
     * Clones this collection.
     *
     * @return AdminAccessRoleCollection Clone of role collection, so changes on clone won't affect original collection.
     */
    public function getClone()
    {
        return clone $this;
    }
    
    
    /**
     * Return this collections valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string Valid type for role collection.
     */
    protected function _getValidType()
    {
        return AdminAccessRoleInterface::class;
    }
}
