<?php
/* --------------------------------------------------------------
   ProductListingPriceStatusHandler.php 2022-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceTextProvider;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceStatusHandler
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceStatusHandler
{
    private ProductListingPriceTextProvider $textProvider;
    
    
    /**
     * ProductListingPriceStatusHandler constructor.
     *
     * @param ProductListingPriceTextProvider $textProvider
     */
    public function __construct(ProductListingPriceTextProvider $textProvider)
    {
        $this->textProvider = $textProvider;
    }
    
    
    /**
     * Finds prices of products which price status is not normal, so either "on request" or "not available for
     * purchase". The price value itself is 0.00, but proper texts are applied based on the price status.
     *
     * `NULL` is returned when the price status is null.
     *
     * @param \Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation $priceStatus
     * @param ListingSettings                                                                                        $listingSettings
     *
     * @return ListingItemPrice|null
     */
    public function findPriceForNotNormalStatus(
        ListingItemPriceInformation $priceStatus,
        ListingSettings             $listingSettings
    ): ?ListingItemPrice {
        if ($priceStatus->isPriceStatusNormal()) {
            return null;
        }
        
        if ($priceStatus->isPriceStatusPriceOnRequest()) {
            return ListingItemPrice::empty($this->textProvider->showPriceOnRequest($listingSettings));
        }
        
        if ($priceStatus->isPriceStatusNotAvailableForPurchase() && $priceStatus->basePrice() <= 0) {
            return ListingItemPrice::empty($this->textProvider->isNotAvailableForPurchase($listingSettings));
        }
        
        return null;
    }
}