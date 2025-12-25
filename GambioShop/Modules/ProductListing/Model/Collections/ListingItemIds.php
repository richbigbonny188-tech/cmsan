<?php
/* --------------------------------------------------------------
   ListingItemIds.php 2022-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\Collections;

use ArrayIterator;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use IteratorAggregate;

/**
 * Class ListingItemIds
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\Collections
 */
class ListingItemIds implements IteratorAggregate
{
    /**
     * @var ListingItemId[]
     */
    private array $ids;
    
    
    /**
     * ListingItemIds constructor.
     *
     * @param ListingItemId ...$ids
     */
    public function __construct(ListingItemId ...$ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * Checks if the id collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->ids);
    }
    
    
    /**
     * Checks if collection contains given id.
     *
     * @param ListingItemId $id
     *
     * @return bool
     */
    public function contains(ListingItemId $id): bool
    {
        foreach ($this->ids as $itemId) {
            if ($id->equals($itemId)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        $cb = static function (ListingItemId $id): int { return $id->asInt(); };
        
        return array_map($cb, $this->ids);
    }
    
    
    /**
     * @inheritDoc
     * @return ListingItemId[]|iterable
     */
    #[\ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->ids);
    }
}