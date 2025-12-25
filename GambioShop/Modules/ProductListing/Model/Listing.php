<?php
/* --------------------------------------------------------------
   Listing.php 2022-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model;

use ArrayIterator;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Entities\ListingItem;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPaginationMeta;
use IteratorAggregate;

/**
 * Class Listing
 *
 * @package Gambio\Shop\Modules\ProductListing\Model
 */
class Listing implements IteratorAggregate
{
    private ListingPaginationMeta $paginationMeta;
    
    /**
     * @var ListingItem[]
     */
    private array $listingItems;
    
    
    /**
     * Listing constructor.
     *
     * @param ListingPaginationMeta $paginationMeta
     * @param ListingItem           ...$listingItems
     */
    public function __construct(ListingPaginationMeta $paginationMeta, ListingItem ...$listingItems)
    {
        $this->paginationMeta = $paginationMeta;
        $this->listingItems   = $listingItems;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array[]
     */
    public function toArray(): array
    {
        $callback = static function (ListingItem $listingItem): array { return $listingItem->toArray(); };
        
        return [
            'data'  => array_map($callback, $this->listingItems),
            '_meta' => $this->paginationMeta->toArray(),
        ];
    }
    
    
    /**
     * Returns a new list containing all listing item ids.
     *
     * @return ListingItemIds
     */
    public function getItemIds(): ListingItemIds
    {
        $cb = static function (ListingItem $item): ListingItemId { return $item->id(); };
        
        return new ListingItemIds(...array_map($cb, $this->listingItems));
    }
    
    
    /**
     * @inheritDoc
     * @return ListingItem[]|iterable
     */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->listingItems);
    }
    
    
    /**
     * Extends a single listing item by the given id.
     *
     * @param ListingItemId $id
     * @param string        $namespace
     * @param mixed         $payload
     *
     * @return void
     */
    public function extendById(ListingItemId $id, string $namespace, $payload): void
    {
        foreach ($this->listingItems as $listingItem) {
            if ($listingItem->hasId($id)) {
                $listingItem->extend($namespace, $payload);
            }
        }
    }
    
    
    /**
     * Extends all items in the listing.
     *
     * @param string $namespace
     * @param mixed  $payload
     *
     * @return void
     */
    public function extend(string $namespace, $payload): void
    {
        foreach ($this->listingItems as $listingItem) {
            $listingItem->extend($namespace, $payload);
        }
    }
    
    
    /**
     * Extends multiple listing items by the given ids.
     *
     * @param ListingItemIds $ids
     * @param string         $namespace
     * @param mixed          $payload
     *
     * @return void
     */
    public function extendByIds(ListingItemIds $ids, string $namespace, $payload): void
    {
        foreach ($this->listingItems as $listingItem) {
            if ($listingItem->contains($ids)) {
                $listingItem->extend($namespace, $payload);
            }
        }
    }
    
    
    /**
     * Extends multiple listing items without given id.
     *
     * @param ListingItemId $id
     * @param string        $namespace
     * @param mixed         $payload
     *
     * @return void
     */
    public function extendWithoutId(ListingItemId $id, string $namespace, $payload): void
    {
        foreach ($this->listingItems as $listingItem) {
            if (!$listingItem->hasId($id)) {
                $listingItem->extend($namespace, $payload);
            }
        }
    }
    
    
    /**
     * Extends multiple listing items without given ids.
     *
     * @param ListingItemIds $ids
     * @param string         $namespace
     * @param mixed          $payload
     *
     * @return void
     */
    public function extendWithoutIds(ListingItemIds $ids, string $namespace, $payload): void
    {
        foreach ($this->listingItems as $listingItem) {
            if (!$listingItem->contains($ids)) {
                $listingItem->extend($namespace, $payload);
            }
        }
    }
}