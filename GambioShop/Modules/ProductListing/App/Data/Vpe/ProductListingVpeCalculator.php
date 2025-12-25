<?php
/*--------------------------------------------------------------
   ProductListingVpeCalculator.php 2023-06-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Vpe;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;

/**
 * Class ProductListingVpeCalculator
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Vpe
 */
class ProductListingVpeCalculator
{
    /**
     * @param ListingItemPrice $price
     * @param float            $vpeValue
     *
     * @return float
     */
    public function calculatePerUnitValue(
        ListingItemPrice $price,
        float            $vpeValue
    ): float {
        ['finalPrice' => $finalPrice] = $price->toArray();
        
        return $finalPrice * (1 / $vpeValue);
    }
}