<?php
/* --------------------------------------------------------------
  ProductListingPriceVariantsHandler.php 2023-06-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceVariantRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper\ProductListingPriceVariantsHandlerHelper;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CheapestPriceNotFoundException;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\ProductNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceVariantsHandler
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceVariantsHandler
{
    private ProductListingPriceVariantsHandlerHelper $helper;
    private ProductListingPriceVariantRepository     $repository;
    private ProductListingPriceFactory                      $factory;


    /**
     * ProductListingPriceVariantsHandler constructor.
     *
     * @param ProductListingPriceVariantsHandlerHelper $helper
     * @param ProductListingPriceVariantRepository     $repository
     * @param ProductListingPriceFactory               $factory
     */
    public function __construct(
        ProductListingPriceVariantsHandlerHelper $helper,
        ProductListingPriceVariantRepository     $repository,
        ProductListingPriceFactory               $factory
    )
    {
        $this->helper     = $helper;
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * Searches for the cheapest variant available.
     *
     * If there is no variants for the product, `NULL` will be returned.
     * When the cheapest option was found, the final price object will be returned.
     *
     * @param ListingItemId               $itemId
     * @param ListingSettings             $listingSettings
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPrice|null
     * @throws ProductNotFoundException
     * @throws CheapestPriceNotFoundException
     */
    public function findVariantsPrice(
        ListingItemId               $itemId,
        ListingSettings             $listingSettings,
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ?ListingItemPrice
    {
        if (false === $this->repository->hasPriceChangingVariant($itemId)) {
            return null;
        }
        
        try {
            $cheapestPrice = $this->repository->getCheapestProductVariantPrice($itemId);
        } catch (CheapestPriceNotFoundException) {
            $cheapestPrice = $this->repository->getCheapestProductOptionPrice($itemId);
        }
        
        $price     = $this->helper->getPrice($priceInformation->basePrice() + $cheapestPrice,
                                             $priceInformation,
                                             $currency);
        $formatted = $this->helper->getFormattedPriceValue($price, $listingSettings, $currency);
        
        
        if ($this->repository->hasSpecial($itemId)) {
            
            $special = $this->helper->getPriceSpecial($itemId, $listingSettings, $priceInformation, $currency);
            
            return $this->factory->createPriceWithSpecial($price, $formatted, $special);
        }
        
        return $this->factory->createPrice($price, $formatted);
    }
}