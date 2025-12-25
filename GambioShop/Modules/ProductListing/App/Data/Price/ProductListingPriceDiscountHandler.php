<?php
/* --------------------------------------------------------------
   ProductListingPriceDiscountHandler.php 2022-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceDiscountRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceTextFormatter;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceVariantRepository;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CheapestPriceNotFoundException;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\ProductNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemGroupSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingPriceDiscountHandler
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceDiscountHandler
{
    private ProductListingPriceDiscountRepository $repository;
    private ProductListingPriceVariantRepository  $variantRepository;
    private ProductListingPriceFactory            $factory;
    private ProductListingPriceTextFormatter      $formatter;
    
    
    /**
     * ProductListingPriceDiscountHandler constructor.
     *
     * @param ProductListingPriceDiscountRepository $repository
     * @param ProductListingPriceVariantRepository  $variantRepository
     * @param ProductListingPriceFactory            $factory
     * @param ProductListingPriceTextFormatter      $formatter
     */
    public function __construct(
        ProductListingPriceDiscountRepository $repository,
        ProductListingPriceVariantRepository  $variantRepository,
        ProductListingPriceFactory            $factory,
        ProductListingPriceTextFormatter      $formatter
    ) {
        $this->repository        = $repository;
        $this->variantRepository = $variantRepository;
        $this->factory           = $factory;
        $this->formatter         = $formatter;
    }
    
    
    /**
     * Handles discount price calculation.
     *
     * @param ListingItemPriceValue    $price
     * @param ListingItemId            $itemId
     * @param ListingItemCurrency      $currency
     * @param ListingItemGroupSettings $groupSettings
     * @param ListingSettings          $listingSettings
     *
     * @return ListingItemPrice|null
     */
    public function handleDiscount(
        ListingItemPriceValue    $price,
        ListingItemId            $itemId,
        ListingItemCurrency      $currency,
        ListingItemGroupSettings $groupSettings,
        ListingSettings          $listingSettings
    ): ?ListingItemPrice {
        $discountRate = $this->repository->findDiscount($itemId, $groupSettings);
        if (!$discountRate) {
            return null;
        }
        
        if ($groupSettings->isVariantsDiscountEnabled() && $this->variantRepository->hasPriceChangingVariant($itemId)) {
            $price = $price->add($this->getCheapestProductVariantPrice($itemId))
                ->add($this->getCheapestProductOptionPrice($itemId));
        }
        
        $discount      = $price->divide(100)->multiply($discountRate);
        $discountPrice = $price->subtract($discount->value());
        $formatted     = $this->formatter->discount($discountPrice, $currency, $listingSettings);
        
        $previousPriceFormatted = $this->formatter->discountPreviousPrice($price, $currency, $listingSettings);
        $previousPrice          = $this->factory->createDiscountPreviousPrice($price->value(), $previousPriceFormatted);
        
        $savingFormatted = $this->formatter->discountSaving($discount, $currency, $listingSettings);
        $saving          = $this->factory->createDiscountSaving($discountRate, $savingFormatted);
        $discount        = $this->factory->createDiscount($previousPrice, $saving);
        
        return $this->factory->createPriceWithDiscount($discountPrice, $formatted, $discount);
    }
    
    
    /**
     * Checks if discount is available.
     *
     * @param ListingItemId            $itemId
     * @param ListingItemGroupSettings $groupSettings
     *
     * @return bool
     */
    public function hasDiscount(ListingItemId $itemId, ListingItemGroupSettings $groupSettings): bool
    {
        return $this->repository->findDiscount($itemId, $groupSettings) !== null;
    }
    
    
    private function getCheapestProductVariantPrice(ListingItemId $itemId): float
    {
        try {
            return $this->variantRepository->getCheapestProductVariantPrice($itemId);
        } catch (CheapestPriceNotFoundException|ProductNotFoundException $e) {
            return 0.0;
        }
    }
    
    
    private function getCheapestProductOptionPrice(ListingItemId $itemId): float
    {
        try {
            return $this->variantRepository->getCheapestProductOptionPrice($itemId);
        } catch (CheapestPriceNotFoundException $e) {
            return 0.0;
        }
    }
}