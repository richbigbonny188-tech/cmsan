<?php
/* --------------------------------------------------------------
   GroupItems.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use IteratorAggregate;
use Traversable;

/**
 * Class AccessGroupItems
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Collections
 */
class AccessGroupItems implements IteratorAggregate
{
    /**
     * @var AccessGroupItem[]
     */
    private $groupItems;
    
    
    /**
     * AccessGroupItems constructor.
     *
     * @param AccessGroupItem ...$groupItems
     */
    private function __construct(AccessGroupItem ...$groupItems)
    {
        $this->groupItems = [];
        foreach ($groupItems as $groupItem) {
            $hash                    = md5($groupItem->type() . '-' . $groupItem->descriptor());
            $this->groupItems[$hash] = $groupItem;
        }
    }
    
    
    /**
     * @param AccessGroupItem ...$groupItems
     *
     * @return AccessGroupItems
     */
    public static function create(AccessGroupItem ...$groupItems): AccessGroupItems
    {
        return new self(...$groupItems);
    }
    
    
    /**
     * @return Traversable|AccessGroupItem[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->groupItems);
    }
    
    
    /**
     * @param AccessGroupItem $groupItem
     *
     * @return AccessGroupItems
     */
    public function withItem(AccessGroupItem $groupItem): AccessGroupItems
    {
        $items        = $this->groupItems;
        $hash         = md5($groupItem->type() . '-' . $groupItem->descriptor());
        $items[$hash] = $groupItem;
        
        return new self(...array_values($items));
    }
    
    
    /**
     * @param AccessGroupItem $groupItem
     *
     * @return AccessGroupItems
     */
    public function withoutItem(AccessGroupItem $groupItem): AccessGroupItems
    {
        $items = $this->groupItems;
        $hash  = md5($groupItem->type() . '-' . $groupItem->descriptor());
        unset($items[$hash]);
        
        return new self(...$items);
    }
}