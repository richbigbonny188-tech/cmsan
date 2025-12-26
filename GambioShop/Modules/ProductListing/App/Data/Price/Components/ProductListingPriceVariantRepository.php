<?php
/* --------------------------------------------------------------
   ProductListingPriceVariantRepository.php 2023-05-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Shop\Modules\ProductListing\App\Exceptions\CheapestPriceNotFoundException;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\ProductNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingPriceVariantRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingPriceVariantRepository
{
    private ProductListingPriceVariantReader           $listingVariantReader;
    private ProductListingPriceOptionReader            $listingOptionReader;
    private ProductListingPriceVariantStockCheckReader $stockCheckReader;
    private ProductListingPriceStockSettingsRepository $stockSettingsRepository;
    private ProductListingPriceVariantCheckReader      $variantCheckReader;
    private ProductListingPriceSpecialRepository       $specialRepository;
    
    
    /**
     * ProductListingPriceVariantRepository constructor.
     *
     * @param ProductListingPriceVariantReader           $listingVariantReader
     * @param ProductListingPriceOptionReader            $listingOptionReader
     * @param ProductListingPriceVariantStockCheckReader $stockCheckReader
     * @param ProductListingPriceStockSettingsRepository $stockSettingsRepository
     * @param ProductListingPriceVariantCheckReader      $variantCheckReader
     * @param ProductListingPriceSpecialRepository       $specialRepository
     */
    public function __construct(
        ProductListingPriceVariantReader           $listingVariantReader,
        ProductListingPriceOptionReader            $listingOptionReader,
        ProductListingPriceVariantStockCheckReader $stockCheckReader,
        ProductListingPriceStockSettingsRepository $stockSettingsRepository,
        ProductListingPriceVariantCheckReader      $variantCheckReader,
        ProductListingPriceSpecialRepository       $specialRepository
    ) {
        $this->listingVariantReader    = $listingVariantReader;
        $this->listingOptionReader     = $listingOptionReader;
        $this->stockCheckReader        = $stockCheckReader;
        $this->stockSettingsRepository = $stockSettingsRepository;
        $this->variantCheckReader      = $variantCheckReader;
        $this->specialRepository       = $specialRepository;
    }
    
    
    /**
     * Provides the price of the cheapest variant.
     *
     * The method throws exceptions if the given listing item id is not associated to a product
     * or if the product don't have variants.
     *
     * @param ListingItemId $id
     *
     * @return float
     * @throws ProductNotFoundException
     * @throws CheapestPriceNotFoundException
     */
    public function getCheapestProductVariantPrice(ListingItemId $id): float
    {
        $stockSettings       = $this->stockSettingsRepository->getStockSettings();
        $isStockCheckEnabled = $this->stockCheckReader->isVariantStockCheckEnabled($id, $stockSettings);
        
        return $isStockCheckEnabled ? $this->listingVariantReader->fetchCheapestVariantPriceWithStock($id) : $this->listingVariantReader->fetchCheapestVariantPriceWithoutStockCheck($id);
    }
    
    
    /**
     * Provides the price of the cheapest option.
     * Throws an exception if given listing item id is not associated to any option.
     *
     * @param ListingItemId $id
     *
     * @return float
     * @throws CheapestPriceNotFoundException
     */
    public function getCheapestProductOptionPrice(ListingItemId $id): float
    {
        return $this->listingOptionReader->fetchCheapestOptionId($id);
    }
    
    
    /**
     * Checks if product has any kind of price changing variants.
     *
     * The check succeeds if the associated product either have product variants
     * or product options with changing prices.
     *
     * @param ListingItemId $id
     *
     * @return bool
     */
    public function hasPriceChangingVariant(ListingItemId $id): bool
    {
        return $this->variantCheckReader->hasPriceChangingProductVariant($id)
               || $this->variantCheckReader->hasPriceChangingProductOption($id);
    }
    
    
    /**
     * @param ListingItemId $itemId
     *
     * @return bool
     */
    public function hasSpecial(ListingItemId $itemId): bool
    {
        return $this->specialRepository->hasSpecial($itemId);
    }
}