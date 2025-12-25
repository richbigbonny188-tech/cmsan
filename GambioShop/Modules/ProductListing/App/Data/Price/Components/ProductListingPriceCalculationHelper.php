<?php
/* --------------------------------------------------------------
   ProductListingPriceCalculationHelper.php 2022-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;

/**
 * Class ProductListingPriceCalculationHelper
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price\Components
 */
class ProductListingPriceCalculationHelper
{
    private ProductListingPriceCalculator $calculator;
    
    
    /**
     * ProductListingPriceCalculationHelper constructor.
     *
     * @param ProductListingPriceCalculator $calculator
     */
    public function __construct(ProductListingPriceCalculator $calculator)
    {
        $this->calculator = $calculator;
    }
    
    
    /**
     * Combines common operations for price calculation.
     *
     * It is a very common process to first add the variant prices when calculating the price, then add the tax and
     * finally apply the currency.
     *
     * @param ListingItemId         $itemId
     * @param ListingItemPriceValue $price
     * @param float                 $taxRate
     * @param ListingItemCurrency   $currency
     *
     * @return ListingItemPriceValue
     */
    public function addCheapestVariantTaxAndApplyCurrency(
        ListingItemId         $itemId,
        ListingItemPriceValue $price,
        float                 $taxRate,
        ListingItemCurrency   $currency
    ): ListingItemPriceValue {
        $price = $this->calculator->addCheapestVariantPrices($itemId, $price);
        $price = $this->calculator->addTax($price, $taxRate);
        
        return $this->calculator->applyCurrency($price, $currency);
    }
    
    
    /**
     * Combines common operations for price calculation.
     *
     * Similar to the other method, it adds taxes to the given price and applies the currency,
     * but is **not** adding any variant related prices.
     *
     * @param ListingItemPriceValue $price
     * @param float                 $taxRate
     * @param ListingItemCurrency   $currency
     *
     * @return ListingItemPriceValue
     */
    public function addTaxAndApplyCurrency(
        ListingItemPriceValue $price,
        float                 $taxRate,
        ListingItemCurrency   $currency
    ): ListingItemPriceValue {
        $price = $this->calculator->addTax($price, $taxRate);
        
        return $this->calculator->applyCurrency($price, $currency);
    }
}