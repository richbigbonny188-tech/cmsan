<?php
/* --------------------------------------------------------------
   ListingFilter.php 2022-01-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Service;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;

/**
 * Interface ListingFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\Service
 */
interface ListingFilter
{
    /**
     * Returns product ids for the listing.
     *
     * This method is the by far most important method. It is responsible to return
     * a list of arbitrary size containing product ids that will be used for the listing.
     *
     * For example, the filter can determine the ids by using any external service, cache them on daily basis and
     * return them.
     *
     * @return ListingItemIds
     */
    public function getProductIds(): ListingItemIds;
    
    
    /**
     * Returns sort order information.
     * The resulting listing will be sorted by this sort order.
     *
     * @return ListingSortOrder
     */
    public function getSortOrder(): ListingSortOrder;
    
    
    /**
     * Returns the listing settings.
     * Those settings contain the language id, customer id (if available) and the currency code.
     *
     * @return ListingSettings
     */
    public function getSettings(): ListingSettings;
    
    
    /**
     * Returns an optional listing event.
     *
     * The event will be dispatched after a listing with this filter
     * is collected and can be used to extend listing items.
     *
     * @param Listing $listing
     *
     * @return object|null
     */
    public function getListingEvent(Listing $listing): ?object;
}
