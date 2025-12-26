<?php
/*--------------------------------------------------------------------------------------------------
    GroupCollection.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Groups\Collections;

use ArrayIterator;
use Gambio\Shop\ProductModifiers\Groups\GroupInterface;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;
use InvalidArgumentException;

/**
 * Class GroupCollection
 * @package Gambio\Shop\ProductModifiers\Groups\Collections
 */
class GroupCollection implements GroupCollectionInterface
{
    /**
     * @var array
     */
    protected $groups = [];
    
    
    /**
     * @inheritDoc
     */
    public function addGroups(GroupCollectionInterface $groups)
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function addGroup(GroupInterface $group)
    {
        $this->groups[] = $group;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->groups);
    }
    
    
    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->groups);
    }
    
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->groups;
    }
    
    
    /**
     * @inheritDoc
     */
    public function get(int $index): GroupInterface
    {
        return $this->groups[$index];
    }
    
    
    /**
     * @inheritDoc
     */
    public function getById(GroupIdentifierInterface $id): GroupInterface
    {
        foreach ($this->groups as $group) {
            if ($id->equals($group->id())) {
                return $group;
            }
        }
        throw new InvalidArgumentException('Invalid Group Identifier');
    }
}