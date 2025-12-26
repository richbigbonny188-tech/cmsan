<?php
/* --------------------------------------------------------------
   ProductListingPriceTextFormatter.php 2023-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceTextFormatter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price\Components
 */
class ProductListingPriceTextFormatter
{
    /**
     * ProductListingPriceTextFormatter constructor.
     *
     * @param ProductListingPriceTextProvider $textProvider
     * @param ProductListingPriceFormatter    $priceFormatter
     */
    public function __construct(
        private ProductListingPriceTextProvider $textProvider,
        private ProductListingPriceFormatter    $priceFormatter
    ) {
    }
    
    
    /**
     * Produces the formatted price text for products with variants.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function variant(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $variantText = $this->textProvider->variant($listingSettings);
        $priceText   = $this->priceFormatter->format($priceValue, $currency);
        
        return "$variantText $priceText";
    }
    
    
    /**
     * Produces the formatted price text for products with variants plus special offer.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function variantWithSpecial(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $variantWithSpecialText = $this->textProvider->variantWithSpecial($listingSettings);
        $priceText              = $this->priceFormatter->format($priceValue, $currency);
        
        return "$variantWithSpecialText $priceText";
    }
    
    
    /**
     * Produces the formatted price text for products with special offer.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function specialOffer(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $specialOfferText = $this->textProvider->specialOffer($listingSettings);
        $priceText        = $this->priceFormatter->format($priceValue, $currency);
        
        return "$specialOfferText $priceText";
    }
    
    
    /**
     * Produces formatted price text for additional special offer information containing the normal price.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function specialOfferBefore(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $specialOfferBeforeText = $this->textProvider->specialOfferBefore($listingSettings);
        $priceText              = $this->priceFormatter->format($priceValue, $currency);
        
        return "<span class=\"productOldPrice\">$specialOfferBeforeText $priceText</span><br />";
    }
    
    
    /**
     * Produces the formatted price text for products with discount.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function discount(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $discount  = $this->textProvider->discount($listingSettings);
        $priceText = $this->priceFormatter->format($priceValue, $currency);
        
        return "$discount $priceText";
    }
    
    
    /**
     * Produces the formatted price text for the discount's normal price.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function discountPreviousPrice(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $discountBeforeText = $this->textProvider->discountPreviousPrice($listingSettings);
        $priceText          = $this->priceFormatter->format($priceValue, $currency);
        
        return "$discountBeforeText $priceText";
    }
    
    
    /**
     * Produces the formatted price text for the discount's savings.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function discountSaving(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $discountSaving = $this->textProvider->discountSaving($listingSettings);
        $priceText      = $this->priceFormatter->format($priceValue, $currency);
        
        return "$discountSaving $priceText";
    }
    
    
    /**
     * Produces the formatted price text for products with personal offers.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function personalOffer(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $personalOffer = $this->textProvider->personalOffer($listingSettings);
        $priceText     = $this->priceFormatter->format($priceValue, $currency);
        
        return "$personalOffer $priceText";
    }
    
    
    /**
     * Produces the formatted price text of the products price without the personal offer.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     * @param ListingSettings       $listingSettings
     *
     * @return string
     */
    public function personalOfferBefore(
        ListingItemPriceValue $priceValue,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        $personalOfferBefore = $this->textProvider->personalOfferBefore($listingSettings);
        $priceText           = $this->priceFormatter->format($priceValue, $currency);
        
        return "$personalOfferBefore $priceText";
    }
}