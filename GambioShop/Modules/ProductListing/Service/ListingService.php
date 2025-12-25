<?php
/*
 * --------------------------------------------------------------
 *   ListingService.php 2022-01-11
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Service;

use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;

/**
 * Interface ListingService
 *
 * @package Gambio\Shop\Modules\ProductListing\Service
 */
interface ListingService
{
    /**
     * Returns a product listing.
     *
     * The filter is responsible to determine the product listings kind.
     * Pagination is used to subdivide the result into several pages,
     * Settings affect the values of the resulting listing.
     *
     * @param ListingFilter     $filter
     * @param ListingPagination $pagination
     *
     * @return Listing
     */
    public function getListing(
        ListingFilter     $filter,
        ListingPagination $pagination
    ): Listing;
}