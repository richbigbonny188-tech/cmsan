<?php
/*--------------------------------------------------------------------------------------------------
    GroupDTOCollection.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups;

use ArrayIterator;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;
use InvalidArgumentException;

/**
 * Class GroupDTOCollection
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups
 */
class GroupDTOCollection implements GroupDTOCollectionInterface
{
    /**
     * @var array
     */
    protected $groups = [];
    
    
    /**
     * @inheritDoc
     */
    public function addGroups(GroupDTOCollectionInterface $groups)
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function addGroup(GroupDTO $group)
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
     * @inheritDoc
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
    public function getById(GroupIdentifierInterface $id): GroupDTO
    {
        foreach ($this->groups as $group) {
            if ($id->equals($group->id())) {
                return $group;
            }
        }
        throw new InvalidArgumentException('Group ID not Identifier!');
    }
}