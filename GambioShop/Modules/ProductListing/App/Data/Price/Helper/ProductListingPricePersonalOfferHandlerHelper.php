<?php
/* --------------------------------------------------------------
   ProductListingPricePersonalOfferHandlerHelper.php 2022-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
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
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPricePersonalOffer;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPricePersonalOfferHandlerHelper
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper
 */
class ProductListingPricePersonalOfferHandlerHelper
{
    private ProductListingPriceCalculationHelper $calculationHelper;
    private ProductListingPriceFactory           $factory;
    private ProductListingPriceTextFormatter     $formatter;
    
    
    public function __construct(
        ProductListingPriceCalculationHelper $calculationHelper,
        ProductListingPriceFactory           $factory,
        ProductListingPriceTextFormatter     $formatter
    ) {
        $this->calculationHelper = $calculationHelper;
        $this->factory           = $factory;
        $this->formatter         = $formatter;
    }
    
    
    public function getPrice(
        float                       $personalOffer,
        ListingItemId               $itemId,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ListingItemPriceValue {
        $priceValue = $this->factory->createPriceValue($personalOffer);
        
        return $this->calculationHelper->addCheapestVariantTaxAndApplyCurrency($itemId,
                                                                               $priceValue,
                                                                               $priceInformation->taxRate(),
                                                                               $currency);
    }
    
    
    public function getFormattedPrice(
        ListingItemPriceValue $price,
        ListingItemCurrency   $currency,
        ListingSettings       $listingSettings
    ): string {
        return $this->formatter->personalOffer($price, $currency, $listingSettings);
    }
    
    
    public function getPersonalOffer(
        ListingItemId               $itemId,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency,
        ListingSettings             $listingSettings
    ): ListingItemPricePersonalOffer {
        $basePrice = $this->factory->createPriceValue($priceInformation->basePrice());
        $basePrice = $this->calculationHelper->addCheapestVariantTaxAndApplyCurrency($itemId,
                                                                                     $basePrice,
                                                                                     $priceInformation->taxRate(),
                                                                                     $currency);
        $formatted = $this->formatter->personalOfferBefore($basePrice, $currency, $listingSettings);
        
        return $this->factory->createPersonalOffer($basePrice->value(), $formatted);
    }
}