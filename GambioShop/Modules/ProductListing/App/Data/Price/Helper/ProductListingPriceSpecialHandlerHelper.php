<?php
/* --------------------------------------------------------------
   ProductListingPriceSpecialHandlerHelper.php 2023-12-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceCalculationHelper;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceTextFormatter;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceSpecial;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceSpecialHandlerHelper
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price\Foo
 */
class ProductListingPriceSpecialHandlerHelper
{
    
    /**
     * @param ProductListingPriceCalculationHelper $calculationHelper
     * @param ProductListingPriceFactory           $factory
     * @param ProductListingPriceTextFormatter     $formatter
     */
    public function __construct(
        private ProductListingPriceCalculationHelper $calculationHelper,
        private ProductListingPriceFactory           $factory,
        private ProductListingPriceTextFormatter     $formatter
    ) {
    }
    
    
    /**
     * Processes the final special offers price value.
     *
     * @param float                       $specialPrice
     * @param ListingItemId               $itemId
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPriceValue
     */
    public function getPrice(
        float                       $specialPrice,
        ListingItemId               $itemId,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ListingItemPriceValue {
        $priceValue = $this->factory->createPriceValue($specialPrice);
        
        return $this->calculationHelper->addCheapestVariantTaxAndApplyCurrency($itemId,
                                                                               $priceValue,
                                                                               $priceInformation->taxRate(),
                                                                               $currency);
    }
    
    
    /**
     * Returns the formatted price value string for the special offer.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingSettings       $listingSettings
     * @param ListingItemCurrency   $currency
     *
     * @return string
     */
    public function getFormattedPriceValue(
        ListingItemPriceValue $priceValue,
        ListingSettings       $listingSettings,
        ListingItemCurrency   $currency
    ): string {
        return $this->formatter->specialOffer($priceValue, $currency, $listingSettings);
    }
    
    
    /**
     * Creates a listing item special instance.
     *
     * @param ListingItemId               $itemId
     * @param ListingSettings             $listingSettings
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPriceSpecial
     */
    public function getPriceSpecial(
        ListingItemId               $itemId,
        ListingSettings             $listingSettings,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ListingItemPriceSpecial {
        $basePriceValue = $this->factory->createPriceValue($priceInformation->basePrice());
        $basePriceValue = $this->calculationHelper->addCheapestVariantTaxAndApplyCurrency($itemId,
                                                                                          $basePriceValue,
                                                                                          $priceInformation->taxRate(),
                                                                                          $currency);
        $baseFormatted  = $this->formatter->specialOfferBefore($basePriceValue, $currency, $listingSettings);
        
        return $this->factory->createSpecial($priceInformation->basePrice(), $baseFormatted);
    }
}