<?php
/*
 * --------------------------------------------------------------
 *   AbstractListingEvent.php 2022-01-10
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Event;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class AbstractListingEvent
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Event
 */
abstract class AbstractListingEvent
{
    private Listing $listing;
    
    
    /**
     * AbstractListingEvent constructor.
     *
     * @param Listing $listing
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
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
        $this->listing->extendById($id, $namespace, $payload);
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
        $this->listing->extend($namespace, $payload);
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
        $this->listing->extendByIds($ids, $namespace, $payload);
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
        $this->listing->extendWithoutId($id, $namespace, $payload);
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
        $this->listing->extendWithoutIds($ids, $namespace, $payload);
    }
}