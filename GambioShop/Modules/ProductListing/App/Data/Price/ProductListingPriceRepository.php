<?php
/* --------------------------------------------------------------
   ProductListingPriceRepository.php 2023-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceTextProvider;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CheapestPriceNotFoundException;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\ProductNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemGroupSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceRepository
{
    /**
     * ProductListingPriceRepository constructor.
     *
     * @param ProductListingPriceHandler              $priceHandler
     * @param ProductListingPriceStatusHandler        $priceStatusHandler
     * @param ProductListingPriceSpecialHandler       $specialHandler
     * @param ProductListingPriceVariantsHandler      $variantsHandler
     * @param ProductListingPricePersonalOfferHandler $personalOfferHandler
     * @param ProductListingPriceDiscountHandler      $discountHandler
     * @param ProductListingPriceTextProvider         $textProvider
     */
    public function __construct(
        private ProductListingPriceHandler              $priceHandler,
        private ProductListingPriceStatusHandler        $priceStatusHandler,
        private ProductListingPriceSpecialHandler       $specialHandler,
        private ProductListingPriceVariantsHandler      $variantsHandler,
        private ProductListingPricePersonalOfferHandler $personalOfferHandler,
        private ProductListingPriceDiscountHandler      $discountHandler,
        private ProductListingPriceTextProvider         $textProvider
    ) {
    }
    
    
    /**
     * Calculates the price of a listing item.
     *
     * The calculations are split across other sub-repositories, for example to handle
     * calculation of specials. This repository glues all the sub-repositories together
     * and defines the execution sequence of the calculations.
     *
     * @param ListingItemId               $itemId
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingSettings             $listingSettings
     * @param ListingItemGroupSettings    $groupSettings
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPrice
     * @throws CheapestPriceNotFoundException
     * @throws ProductNotFoundException
     */
    public function getPrice(
        ListingItemId               $itemId,
        ListingItemPriceInformation $priceInformation,
        ListingSettings             $listingSettings,
        ListingItemGroupSettings    $groupSettings,
        ListingItemCurrency         $currency
    ): ListingItemPrice {
        if ($price = $this->isNotAllowedToSeePrices($listingSettings, $groupSettings)) {
            return $price;
        }
        if ($price = $this->hasNonNormalPriceStatus($priceInformation, $listingSettings)) {
            return $price;
        }
        if ($price = $this->hasVariants($itemId, $listingSettings, $priceInformation, $currency)) {
            return $price;
        }
        if ($price = $this->hasSpecial($itemId, $listingSettings, $priceInformation, $currency)) {
            return $price;
        }
        if ($price = $this->hasPersonalOffer($itemId, $priceInformation, $currency, $groupSettings, $listingSettings)) {
            return $price;
        }
        if ($this->discountHandler->hasDiscount($itemId, $groupSettings)) {
            $priceValue = ListingItemPriceValue::create($priceInformation->basePrice());
            
            return $this->discountHandler->handleDiscount($priceValue,
                                                          $itemId,
                                                          $currency,
                                                          $groupSettings,
                                                          $listingSettings);
        }
        
        return $this->priceHandler->getNormalPrice($priceInformation, $currency);
    }
    
    
    /**
     * Some customer groups are not allowed to see prices. If it is allowed, this method returns null.
     *
     * Otherwise, it returns a price instance containing information that the customer is not
     * allowed to see prices.
     *
     * @param ListingSettings          $listingSettings
     * @param ListingItemGroupSettings $groupSettings
     *
     * @return ListingItemPrice|null
     */
    private function isNotAllowedToSeePrices(
        ListingSettings          $listingSettings,
        ListingItemGroupSettings $groupSettings
    ): ?ListingItemPrice {
        if ($groupSettings->isAllowedToSeePrices()) {
            return null;
        }
        
        return ListingItemPrice::empty($this->textProvider->notAllowedToSeePrices($listingSettings));
    }
    
    
    /**
     * Returns `NULL` if the price status is normal.
     *
     * Otherwise, it returns a price instance containing information
     * about the special price status.
     *
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingSettings             $listingSettings
     *
     * @return ListingItemPrice|null
     */
    private function hasNonNormalPriceStatus(
        ListingItemPriceInformation $priceInformation,
        ListingSettings             $listingSettings
    ): ?ListingItemPrice {
        return $this->priceStatusHandler->findPriceForNotNormalStatus($priceInformation, $listingSettings);
    }
    
    
    /**
     * Returns `NULL` if there is no special offer for the listing item.
     * Otherwise, a price instance containing the special offer information is returned.
     *
     * @param ListingItemId               $itemId
     * @param ListingSettings             $listingSettings
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPrice|null
     */
    private function hasSpecial(
        ListingItemId               $itemId,
        ListingSettings             $listingSettings,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ?ListingItemPrice {
        return $this->specialHandler->findSpecialPrice($itemId,
                                                       $listingSettings,
                                                       $priceInformation,
                                                       $currency);
    }
    
    
    /**
     * Returns  `NULL` if there are no variants associated with the listing item.
     *
     * Otherwise, returns the cheapest available variant.
     *
     * @param ListingItemId               $itemId
     * @param ListingSettings             $listingSettings
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPrice|null
     * @throws CheapestPriceNotFoundException
     * @throws ProductNotFoundException
     */
    private function hasVariants(
        ListingItemId               $itemId,
        ListingSettings             $listingSettings,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ?ListingItemPrice {
        return $this->variantsHandler->findVariantsPrice($itemId,
                                                         $listingSettings,
                                                         $priceInformation,
                                                         $currency);
    }
    
    
    /**
     * Returns `NULL` if there is no personal offer for the listing item.
     *
     * Otherwise, returns the personal offer including additional.
     * If the listing item is discounted, the discount value is calculated based on the personal offer.
     *
     * @param ListingItemId               $itemId
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     * @param ListingItemGroupSettings    $groupSettings
     * @param ListingSettings             $listingSettings
     *
     * @return ListingItemPrice|null
     */
    private function hasPersonalOffer(
        ListingItemId               $itemId,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency,
        ListingItemGroupSettings    $groupSettings,
        ListingSettings             $listingSettings
    ): ?ListingItemPrice {
        return $this->personalOfferHandler->findPersonalOffer($itemId,
                                                              $priceInformation,
                                                              $currency,
                                                              $groupSettings,
                                                              $listingSettings);
    }
}
