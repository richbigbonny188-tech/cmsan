<?php
/* --------------------------------------------------------------
   AccessGroup.php 2021-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model;

use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroupItems;
use Gambio\Admin\Modules\AccessGroup\Model\Events\ItemFromAccessGroupRemoved;
use Gambio\Admin\Modules\AccessGroup\Model\Events\ItemToAccessGroupAdded;
use Gambio\Admin\Modules\AccessGroup\Model\Events\NamesAndDescriptionsUpdated;
use Gambio\Admin\Modules\AccessGroup\Model\Events\SortOrderUpdated;
use Gambio\Admin\Modules\AccessGroup\Model\Exceptions\ParentAccessGroupIdDoesNotExistException;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupDescriptions;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupNames;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\ParentAccessGroupId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class AccessGroup
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model
 */
class AccessGroup extends AbstractEventRaisingEntity
{
    /**
     * @var AccessGroupId
     */
    private $id;
    
    /**
     * @var ParentAccessGroupId|null
     */
    private $parent;
    
    /**
     * @var AccessGroupNames
     */
    private $names;
    
    /**
     * @var AccessGroupDescriptions
     */
    private $descriptions;
    
    /**
     * @var AccessGroupItems
     */
    private $items;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    /**
     * @var bool
     */
    private $isProtected;
    
    
    /**
     * AccessGroup constructor.
     *
     * @param AccessGroupId            $id
     * @param ParentAccessGroupId|null $parent
     * @param AccessGroupNames         $names
     * @param AccessGroupDescriptions  $descriptions
     * @param AccessGroupItems         $items
     * @param int                      $sortOrder
     * @param bool                     $isProtected
     */
    private function __construct(
        AccessGroupId           $id,
        ?ParentAccessGroupId    $parent,
        AccessGroupNames        $names,
        AccessGroupDescriptions $descriptions,
        AccessGroupItems        $items,
        int                     $sortOrder,
        bool                    $isProtected
    ) {
        $this->id           = $id;
        $this->parent       = $parent;
        $this->names        = $names;
        $this->descriptions = $descriptions;
        $this->items        = $items;
        $this->sortOrder    = $sortOrder;
        $this->isProtected  = $isProtected;
    }
    
    
    /**
     * @param AccessGroupId           $id
     * @param ParentAccessGroupId     $parent
     * @param AccessGroupNames        $names
     * @param AccessGroupDescriptions $descriptions
     * @param AccessGroupItems        $items
     * @param int                     $sortOrder
     * @param bool                    $isProtected
     *
     * @return AccessGroup
     */
    public static function createWithParent(
        AccessGroupId           $id,
        ParentAccessGroupId     $parent,
        AccessGroupNames        $names,
        AccessGroupDescriptions $descriptions,
        AccessGroupItems        $items,
        int                     $sortOrder,
        bool                    $isProtected
    ): AccessGroup {
        return new self($id, $parent, $names, $descriptions, $items, $sortOrder, $isProtected);
    }
    
    
    /**
     * @param AccessGroupId           $id
     * @param AccessGroupNames        $names
     * @param AccessGroupDescriptions $descriptions
     * @param AccessGroupItems        $items
     * @param int                     $sortOrder
     * @param bool                    $isProtected
     *
     * @return AccessGroup
     */
    public static function createWithoutParent(
        AccessGroupId           $id,
        AccessGroupNames        $names,
        AccessGroupDescriptions $descriptions,
        AccessGroupItems        $items,
        int                     $sortOrder,
        bool                    $isProtected
    ): AccessGroup {
        return new self($id, null, $names, $descriptions, $items, $sortOrder, $isProtected);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return int
     *
     * @throws ParentAccessGroupIdDoesNotExistException
     */
    public function parentGroupId(): int
    {
        if ($this->parent === null) {
            throw ParentAccessGroupIdDoesNotExistException::forGroup($this->id());
        }
        
        return $this->parent->value();
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function name(string $languageCode): string
    {
        return $this->names->getName($languageCode);
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function description(string $languageCode): string
    {
        return $this->descriptions->getDescription($languageCode);
    }
    
    
    /**
     * @return AccessGroupItems
     */
    public function groupItems(): AccessGroupItems
    {
        return $this->items;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * @return bool
     */
    public function isProtected(): bool
    {
        return $this->isProtected;
    }
    
    
    /**
     * @param AccessGroupNames        $names
     * @param AccessGroupDescriptions $descriptions
     */
    public function updateNamesAndDescriptions(
        AccessGroupNames        $names,
        AccessGroupDescriptions $descriptions
    ): void {
        $this->names        = $names;
        $this->descriptions = $descriptions;
        
        $this->raiseEvent(NamesAndDescriptionsUpdated::create($this->id, $this->names, $this->descriptions));
    }
    
    
    /**
     * @param int $sortOrder
     */
    public function updateSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
        
        $this->raiseEvent(SortOrderUpdated::create($this->id, $this->sortOrder));
    }
    
    
    /**
     * @param AccessGroupItem $groupItem
     */
    public function addItem(AccessGroupItem $groupItem): void
    {
        $this->items = $this->items->withItem($groupItem);
        
        $this->raiseEvent(ItemToAccessGroupAdded::create($this->id, $groupItem));
    }
    
    
    /**
     * @param AccessGroupItem $groupItem
     */
    public function removeItem(AccessGroupItem $groupItem): void
    {
        $this->items = $this->items->withoutItem($groupItem);
        
        $this->raiseEvent(ItemFromAccessGroupRemoved::create($this->id, $groupItem));
    }
}