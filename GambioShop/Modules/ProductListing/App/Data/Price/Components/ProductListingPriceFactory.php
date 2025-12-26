<?php
/* --------------------------------------------------------------
   ProductListingPriceFactory.php 2022-08-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceDiscount;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceDiscountPreviousPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceDiscountSaving;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPricePersonalOffer;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceSpecial;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;

/**
 * Class ProductListingPriceFactory
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price\Components
 */
class ProductListingPriceFactory
{
    /**
     * Creates a listing item price.
     *
     * This price **does not** contain any additional information,
     * like specials, discounts or personal offers.
     *
     * @param ListingItemPriceValue $priceValue
     * @param string                $formatted
     *
     * @return ListingItemPrice
     */
    public function createPrice(ListingItemPriceValue $priceValue, string $formatted): ListingItemPrice
    {
        return new ListingItemPrice($priceValue, $formatted);
    }
    
    
    /**
     * Creates a listing item with additional information about the special offer.
     *
     * @param ListingItemPriceValue   $priceValue
     * @param string                  $formatted
     * @param ListingItemPriceSpecial $special
     *
     * @return ListingItemPrice
     */
    public function createPriceWithSpecial(
        ListingItemPriceValue   $priceValue,
        string                  $formatted,
        ListingItemPriceSpecial $special
    ): ListingItemPrice {
        return new ListingItemPrice($priceValue, $formatted, $special);
    }
    
    
    /**
     * Creates a listing item with additional information about the customers discount.
     *
     * @param ListingItemPriceValue    $priceValue
     * @param string                   $formatted
     * @param ListingItemPriceDiscount $discount
     *
     * @return ListingItemPrice
     */
    public function createPriceWithDiscount(
        ListingItemPriceValue    $priceValue,
        string                   $formatted,
        ListingItemPriceDiscount $discount
    ): ListingItemPrice {
        return new ListingItemPrice($priceValue, $formatted, $discount);
    }
    
    
    /**
     * Creates the internal price value, used for calculations.
     *
     * @param float $price
     *
     * @return ListingItemPriceValue
     */
    public function createPriceValue(float $price): ListingItemPriceValue
    {
        return ListingItemPriceValue::create($price);
    }
    
    
    /**
     * Creates a listing item price discount.
     *
     * @param ListingItemPriceDiscountPreviousPrice $previousPrice
     * @param ListingItemPriceDiscountSaving        $saving
     *
     * @return ListingItemPriceDiscount
     */
    public function createDiscount(
        ListingItemPriceDiscountPreviousPrice $previousPrice,
        ListingItemPriceDiscountSaving        $saving
    ): ListingItemPriceDiscount {
        return new ListingItemPriceDiscount($previousPrice, $saving);
    }
    
    
    /**
     * Creates the discount previous price information.
     *
     * @param float  $value
     * @param string $formatted
     *
     * @return ListingItemPriceDiscountPreviousPrice
     */
    public function createDiscountPreviousPrice(float $value, string $formatted): ListingItemPriceDiscountPreviousPrice
    {
        return new ListingItemPriceDiscountPreviousPrice($value, $formatted);
    }
    
    
    /**
     * Creates the discount saving information.
     *
     * @param float  $percentage
     * @param string $formatted
     *
     * @return ListingItemPriceDiscountSaving
     */
    public function createDiscountSaving(float $percentage, string $formatted): ListingItemPriceDiscountSaving
    {
        return new ListingItemPriceDiscountSaving($percentage, $formatted);
    }
    
    
    /**
     * Creates a listing item price with additional information about the personal offer for the customer.
     *
     * @param ListingItemPriceValue         $priceValue
     * @param string                        $formatted
     * @param ListingItemPricePersonalOffer $personalOffer
     *
     * @return ListingItemPrice
     */
    public function createPriceWithPersonalOffer(
        ListingItemPriceValue         $priceValue,
        string                        $formatted,
        ListingItemPricePersonalOffer $personalOffer
    ): ListingItemPrice {
        return new ListingItemPrice($priceValue, $formatted, $personalOffer);
    }
    
    
    /**
     * Creates additional information for listing items with a personal offer.
     *
     * @param float  $normalPrice
     * @param string $formatted
     *
     * @return ListingItemPricePersonalOffer
     */
    public function createPersonalOffer(float $normalPrice, string $formatted): ListingItemPricePersonalOffer
    {
        return new ListingItemPricePersonalOffer($normalPrice, $formatted);
    }
    
    
    /**
     * Creates additional information for listing items with a special offer.
     *
     * @param float  $normalPrice
     * @param string $formatted
     *
     * @return ListingItemPriceSpecial
     */
    public function createSpecial(float $normalPrice, string $formatted): ListingItemPriceSpecial
    {
        return new ListingItemPriceSpecial($normalPrice, $formatted);
    }
}