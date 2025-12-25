<?php

/* --------------------------------------------------------------
   EditableCollection.php 2016-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EditableCollection
 *
 * @category System
 * @package  Shared
 */
class EditableCollection extends AbstractCollection
{
    /**
     * Sets an item to the collection.
     * If the index already exists, the value gets override.
     *
     * @param int|IntType $index Gets validate internally with the IntType.
     * @param mixed       $value
     *
     * @return $this Same instance to make chained method calls possible.
     * @throws InvalidArgumentException When value has an invalid type.
     */
    public function setItem($index, $value)
    {
        // passed args through type objects to force their validation without using type hints.
        new IntType($index);
        
        if (!$this->_itemIsValid($value)) {
            $exceptionText = $this->_getExceptionText();
            throw new InvalidArgumentException($exceptionText);
        }
        $this->collectionContentArray[$index] = $value;
        
        return $this;
    }
    
    
    /**
     * Adds a new item to the collection.
     *
     * @param mixed $value
     *
     * @return $this Same instance to make chained method calls possible.
     * @throws InvalidArgumentException When value has an invalid type.
     */
    public function addItem($value)
    {
        $this->_add($value);
        
        return $this;
    }
    
    
    /**
     * Add another collection to this one.
     *
     * @param EditableCollection $collection Collection to add.
     *
     * @return $this Same instance to make chained method calls possible.
     * @throws InvalidArgumentException When the item types of the passed collection are invalid.
     */
    public function addCollection(EditableCollection $collection)
    {
        foreach ($collection->getArray() as $collectionItem) {
            $this->_add($collectionItem);
        }
        
        return $this;
    }
    
    
    /**
     * Return a clone of the current editable key value collection instance.
     */
    public function getClone()
    {
        return clone $this;
    }
    
    
    /**
     * Get valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string
     */
    protected function _getValidType()
    {
        return 'string';
    }
}
