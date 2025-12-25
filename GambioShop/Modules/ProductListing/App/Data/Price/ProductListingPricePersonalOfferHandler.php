<?php
/* --------------------------------------------------------------
   ProductListingPricePersonalOfferHandler.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPricePersonalOfferRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper\ProductListingPricePersonalOfferHandlerHelper;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemGroupSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPricePersonalOfferHandler
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPricePersonalOfferHandler
{
    private ProductListingPricePersonalOfferRepository    $repository;
    private ProductListingPriceDiscountHandler            $discountHandler;
    private ProductListingPricePersonalOfferHandlerHelper $helper;
    private ProductListingPriceFactory                    $factory;
    
    
    /**
     * ProductListingPricePersonalOfferHandler constructor.
     *
     * @param ProductListingPricePersonalOfferRepository    $repository
     * @param ProductListingPriceDiscountHandler            $discountHandler
     * @param ProductListingPricePersonalOfferHandlerHelper $helper
     * @param ProductListingPriceFactory                    $factory
     */
    public function __construct(
        ProductListingPricePersonalOfferRepository    $repository,
        ProductListingPriceDiscountHandler            $discountHandler,
        ProductListingPricePersonalOfferHandlerHelper $helper,
        ProductListingPriceFactory                    $factory
    ) {
        $this->repository      = $repository;
        $this->discountHandler = $discountHandler;
        $this->helper          = $helper;
        $this->factory         = $factory;
    }
    
    
    /**
     * Tries to find a personal offer for the listing item.
     * When the listing item contains a discount, the discounted price is returned.
     *
     * @param ListingItemId                                                                                          $itemId
     * @param \Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency                                                                                    $currency
     * @param ListingItemGroupSettings                                                                               $groupSettings
     * @param ListingSettings                                                                                        $listingSettings
     *
     * @return ListingItemPrice|null
     */
    public function findPersonalOffer(
        ListingItemId               $itemId,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency,
        ListingItemGroupSettings    $groupSettings,
        ListingSettings             $listingSettings
    ): ?ListingItemPrice {
        $personalOffer = $this->repository->findPersonalOffer($itemId, $groupSettings);
        if (null === $personalOffer) {
            return null;
        }
        
        $personalOfferPrice = $this->helper->getPrice($personalOffer, $itemId, $priceInformation, $currency);
        $discount           = $this->discountHandler->handleDiscount($personalOfferPrice,
                                                                     $itemId,
                                                                     $currency,
                                                                     $groupSettings,
                                                                     $listingSettings);
        if ($discount) {
            return $discount;
        }
        
        $formatted     = $this->helper->getFormattedPrice($personalOfferPrice, $currency, $listingSettings);
        $personalOffer = $this->helper->getPersonalOffer($itemId, $priceInformation, $currency, $listingSettings);
        
        return $this->factory->createPriceWithPersonalOffer($personalOfferPrice, $formatted, $personalOffer);
    }
}