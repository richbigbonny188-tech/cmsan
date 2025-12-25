<?php
/* --------------------------------------------------------------
  ProductListingShippingModelFactory.php 2023-01-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Shipping;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShipping;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShippingRange;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShippingRangeBound;

/**
 * Class ProductListingShippingModelFactory
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Shipping
 */
class ProductListingShippingModelFactory
{
    /**
     * Creates listing item shipping.
     *
     * @param int                           $days
     * @param string                        $name
     * @param string                        $image
     * @param bool                          $linkIsActive
     * @param ListingItemShippingRange|null $shippingRange
     *
     * @return ListingItemShipping
     */
    public function createListingItemShipping(
        int                      $days,
        string                   $name,
        string                   $image,
        bool                     $linkIsActive,
        ?ListingItemShippingRange $shippingRange
    ): ListingItemShipping
    {
        return new ListingItemShipping($days, $name, $image, $linkIsActive, $shippingRange);
    }


    /**
     * Creates listing item shipping range.
     *
     * @param ListingItemShippingRangeBound $min
     * @param ListingItemShippingRangeBound $max
     *
     * @return ListingItemShippingRange
     */
    public function createListingItemShippingRange(
        ListingItemShippingRangeBound $min,
        ListingItemShippingRangeBound $max
    ): ListingItemShippingRange
    {
        return new ListingItemShippingRange($min, $max);
    }


    /**
     * Creates listing item shipping range bound.
     *
     * @param int $days
     * @param string $name
     * @param string $image
     *
     * @return ListingItemShippingRangeBound
     */
    public function createListingItemShippingRangeBound(
        int    $days,
        string $name,
        string $image
    ): ListingItemShippingRangeBound
    {
        return new ListingItemShippingRangeBound($days, $name, $image);
    }
}