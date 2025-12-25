<?php
/* --------------------------------------------------------------
   ListingExtender.php 2022-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Service;

use Gambio\Shop\Modules\ProductListing\Model\Listing;

/**
 * Interface ListingExtender
 *
 * @package Gambio\Shop\Modules\ProductListing\Service
 */
interface ListingExtender
{
    /**
     * Extends the listing.
     *
     * This method is intended to use to extend the given listing instance. The filter can be used, for example,
     * to only apply the extending mechanism on specific filter (ListingFilter implementations represent the
     * kind of listing).
     *
     * @param Listing       $listing
     * @param ListingFilter $listingFilter
     */
    public function extend(Listing $listing, ListingFilter $listingFilter): void;
}