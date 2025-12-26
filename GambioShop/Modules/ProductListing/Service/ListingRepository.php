<?php
/* --------------------------------------------------------------
   ListingRepository.php 2022-01-07
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
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;

/**
 * Interface ConfigurationFinder
 *
 * @package Gambio\Shop\Modules\ProductListing\Service
 */
interface ListingRepository
{
    /**
     * Loads product listing data.
     *
     * Ids are used product related data. Pagination is used to limit the result set.
     * Settings might affect the resulting values and the sort order changes, as the name implies, the sort order.
     *
     * @param ListingItemIds    $ids
     * @param ListingPagination $pagination
     * @param ListingSettings   $settings
     * @param ListingSortOrder  $sortOrder
     *
     * @return Listing
     */
    public function getListing(
        ListingItemIds    $ids,
        ListingPagination $pagination,
        ListingSettings   $settings,
        ListingSortOrder  $sortOrder
    ): Listing;
}
