<?php
/* --------------------------------------------------------------
   ListingItem.php 2022-07-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\Entities;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDates;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDetails;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImages;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemManufacturer;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemMeta;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShipping;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemStock;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemTax;

/**
 * Class ListingItem
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\Entities
 */
class ListingItem
{
    private ListingItemId            $id;
    private ListingItemDetails       $details;
    private ListingItemSettings      $settings;
    private ListingItemShipping      $shipping;
    private ListingItemStock         $stock;
    private ListingItemMeta          $meta;
    private ListingItemTax           $tax;
    private ListingItemPrice         $price;
    private ListingItemDates         $dates;
    private ListingItemImages        $images;
    private ?ListingItemManufacturer $manufacturer;
    private array                    $extensions = [];
    
    
    /**
     * ListingItem constructor.
     *
     * @param ListingItemId                $id
     * @param ListingItemDetails           $details
     * @param ListingItemSettings          $settings
     * @param ListingItemShipping          $shipping
     * @param ListingItemStock             $stock
     * @param ListingItemMeta              $meta
     * @param ListingItemTax               $tax
     * @param ListingItemPrice             $price
     * @param ListingItemDates             $dates
     * @param ListingItemImages            $images
     * @param ListingItemManufacturer|null $manufacturer
     */
    public function __construct(
        ListingItemId            $id,
        ListingItemDetails       $details,
        ListingItemSettings      $settings,
        ListingItemShipping      $shipping,
        ListingItemStock         $stock,
        ListingItemMeta          $meta,
        ListingItemTax           $tax,
        ListingItemPrice         $price,
        ListingItemDates         $dates,
        ListingItemImages        $images,
        ?ListingItemManufacturer $manufacturer
    ) {
        $this->id           = $id;
        $this->details      = $details;
        $this->settings     = $settings;
        $this->shipping     = $shipping;
        $this->stock        = $stock;
        $this->meta         = $meta;
        $this->tax          = $tax;
        $this->price        = $price;
        $this->dates        = $dates;
        $this->images       = $images;
        $this->manufacturer = $manufacturer;
    }
    
    
    /**
     * Returns the id of the listing item.
     *
     * @return ListingItemId
     */
    public function id(): ListingItemId
    {
        return new ListingItemId($this->id->asInt());
    }
    
    
    /**
     * Checks if listing item contains given id.
     *
     * @param ListingItemId $id
     *
     * @return bool
     */
    public function hasId(ListingItemId $id): bool
    {
        return $this->id->equals($id);
    }
    
    
    /**
     * Checks if the listing item matches on of the given ids.
     *
     * @param ListingItemIds $ids
     *
     * @return bool
     */
    public function contains(ListingItemIds $ids): bool
    {
        foreach ($ids as $id) {
            if ($this->hasId($id)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Extends a listing item.
     *
     * Adds the payload in the given namespace of the internal extensions array. Calling
     * this method multiple times will append the payload to the namespace extension array.
     *
     * @param string $namespace
     * @param mixed  $payload
     *
     * @return void
     */
    public function extend(string $namespace, $payload): void
    {
        $this->extensions[$namespace][] = $payload;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'id'           => $this->id->asInt(),
            'details'      => $this->details->toArray(),
            'settings'     => $this->settings->toArray(),
            'shipping'     => $this->shipping->toArray(),
            'stock'        => $this->stock->toArray(),
            'meta'         => $this->meta->toArray(),
            'tax'          => $this->tax->toArray(),
            'price'        => $this->price->toArray(),
            'dates'        => $this->dates->toArray(),
            'images'       => $this->images->toArray(),
            'manufacturer' => $this->manufacturer ? $this->manufacturer->toArray() : null,
            'extensions'   => $this->extensions,
        ];
        if ($this->manufacturer) {
            $data['manufacturer'] = $this->manufacturer->toArray();
        }
        
        return $data;
    }
}