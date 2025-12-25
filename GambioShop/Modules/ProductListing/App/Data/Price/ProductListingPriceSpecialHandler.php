<?php
/* --------------------------------------------------------------
   ProductListingPriceSpecialHandler.php 2023-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceSpecialRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper\ProductListingPriceSpecialHandlerHelper;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceSpecialHandler
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceSpecialHandler
{
    
    /**
     * ProductListingPriceSpecialHandler constructor.
     *
     * @param ProductListingPriceSpecialHandlerHelper $helper
     * @param ProductListingPriceSpecialRepository    $repository
     * @param ProductListingPriceFactory              $factory
     */
    public function __construct(
        private ProductListingPriceSpecialHandlerHelper $helper,
        private ProductListingPriceSpecialRepository    $repository,
        private ProductListingPriceFactory              $factory
    ) {
    }
    
    
    /**
     * Searches for a special offer.
     *
     * If there is no special offer for the product, `NULL` will be returned.
     * When a special offer was found, the final price object will be returned.
     *
     * @param ListingItemId               $itemId
     * @param ListingSettings             $listingSettings
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPrice|null
     */
    public function findSpecialPrice(
        ListingItemId               $itemId,
        ListingSettings             $listingSettings,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ?ListingItemPrice {
        $specialPrice = $this->repository->findSpecialPrice($itemId);
        
        if (!$specialPrice) {
            return null;
        }
        
        $price     = $this->helper->getPrice($specialPrice, $itemId, $priceInformation, $currency);
        $formatted = $this->helper->getFormattedPriceValue($price, $listingSettings, $currency);
        
        return $this->factory->createPrice($price, $formatted);
    }
}