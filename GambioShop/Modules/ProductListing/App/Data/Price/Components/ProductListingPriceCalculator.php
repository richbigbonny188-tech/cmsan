<?php
/* --------------------------------------------------------------
   ProductListingPriceCalculator.php 2022-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Shop\Modules\ProductListing\App\Exceptions\CheapestPriceNotFoundException;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\ProductNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;

/**
 * Class ProductListingPriceCalculator
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price\Components
 */
class ProductListingPriceCalculator
{
    private ProductListingPriceVariantRepository $variantRepository;
    
    
    /**
     * ProductListingPriceCalculator constructor.
     *
     * @param ProductListingPriceVariantRepository $variantRepository
     */
    public function __construct(ProductListingPriceVariantRepository $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }
    
    
    /**
     * Adds the cheapest product variant and product option price to the passed price value argument.
     *
     * The method can be used safely, as all errors are handled internally.
     * In case of need the method just returns the price value argument again.
     *
     * @param ListingItemId         $itemId
     * @param ListingItemPriceValue $priceValue
     *
     * @return ListingItemPriceValue
     */
    public function addCheapestVariantPrices(ListingItemId         $itemId,
                                             ListingItemPriceValue $priceValue
    ): ListingItemPriceValue {
        $priceValue = $this->tryAddCheapestProductVariantPrice($itemId, $priceValue);
        
        return $this->tryAddCheapestProductOptionPrice($itemId, $priceValue);
    }
    
    
    /**
     * Adds the tax rate to the current price.
     *
     * @param ListingItemPriceValue $price
     * @param float                 $taxRate
     *
     * @return ListingItemPriceValue
     */
    public function addTax(ListingItemPriceValue $price, float $taxRate): ListingItemPriceValue
    {
        $tax = $price->divide(100)->multiply($taxRate);
        
        return $price->add($tax->value());
    }
    
    
    /**
     * Applies currency conversion by multiply with the currencies value and rounding.
     *
     * @param ListingItemPriceValue $price
     * @param ListingItemCurrency   $currency
     *
     * @return ListingItemPriceValue
     */
    public function applyCurrency(
        ListingItemPriceValue $price,
        ListingItemCurrency   $currency
    ): ListingItemPriceValue {
        return $price->multiply($currency->value())->round($currency->decimalPlaces());
    }
    
    
    /**
     * Tries to add the cheapest product variant price.
     *
     * @param ListingItemId         $itemId
     * @param ListingItemPriceValue $priceValue
     *
     * @return ListingItemPriceValue
     */
    private function tryAddCheapestProductVariantPrice(
        ListingItemId         $itemId,
        ListingItemPriceValue $priceValue
    ): ListingItemPriceValue {
        try {
            $cheapestProductVariantPrice = $this->variantRepository->getCheapestProductVariantPrice($itemId);
            if ($cheapestProductVariantPrice > 0) {
                $priceValue = $priceValue->add($cheapestProductVariantPrice);
            }
        } catch (CheapestPriceNotFoundException|ProductNotFoundException $e) {
        }
        
        return $priceValue;
    }
    
    
    /**
     * Tries to add the cheapest product option price.
     *
     * @param ListingItemId         $itemId
     * @param ListingItemPriceValue $priceValue
     *
     * @return ListingItemPriceValue
     */
    private function tryAddCheapestProductOptionPrice(
        ListingItemId         $itemId,
        ListingItemPriceValue $priceValue
    ): ListingItemPriceValue {
        try {
            $cheapestProductOptionPrice = $this->variantRepository->getCheapestProductOptionPrice($itemId);
            if ($cheapestProductOptionPrice > 0) {
                $priceValue = $priceValue->add($cheapestProductOptionPrice);
            }
        } catch (CheapestPriceNotFoundException $e) {
        }
        
        return $priceValue;
    }
}