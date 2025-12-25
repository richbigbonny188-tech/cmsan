<?php
/* --------------------------------------------------------------
   ProductListingTaxRepository.php 2023-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Tax;

use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingModelFactory;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemTax;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingTaxRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxRepository
{
    private ProductListingTaxReader     $taxReader;
    private ProductListingTaxCalculator $taxCalculator;
    private ProductListingTaxFormatter  $taxFormatter;
    private ProductListingModelFactory  $factory;
    
    /**
     * @var array<string, ListingItemTax>
     */
    private array $taxCache = [];
    
    
    /**
     * ProductListingTaxRepository constructor.
     *
     * @param ProductListingTaxReader     $taxReader
     * @param ProductListingTaxCalculator $taxCalculator
     * @param ProductListingTaxFormatter  $taxFormatter
     * @param ProductListingModelFactory  $factory
     */
    public function __construct(
        ProductListingTaxReader     $taxReader,
        ProductListingTaxCalculator $taxCalculator,
        ProductListingTaxFormatter  $taxFormatter,
        ProductListingModelFactory  $factory
    ) {
        $this->taxReader     = $taxReader;
        $this->taxCalculator = $taxCalculator;
        $this->taxFormatter  = $taxFormatter;
        $this->factory       = $factory;
    }
    
    
    /**
     * Returns tax information of a listing item based on the products tax class id.
     *
     * @param int             $taxClassId
     * @param ListingSettings $listingSettings
     *
     * @return ListingItemTax
     */
    public function getListingItemTax(int $taxClassId, ListingSettings $listingSettings): ListingItemTax
    {
        $cacheKey = $this->generateCacheKey($taxClassId, $listingSettings);
        if (!array_key_exists($cacheKey, $this->taxCache)) {
            $this->taxCache[$cacheKey] = $this->getTax($taxClassId, $listingSettings);
        }
        
        return $this->taxCache[$cacheKey];
    }
    
    
    /**
     * Creates tax information for the listing item.
     *
     * @param int             $taxClassId
     * @param ListingSettings $listingSettings
     *
     * @return ListingItemTax
     */
    private function getTax(int $taxClassId, ListingSettings $listingSettings): ListingItemTax
    {
        $taxClass = $this->taxReader->fetchClass($taxClassId);
        $taxRates = $this->taxReader->fetchRates($taxClassId, $listingSettings->languageId());
        
        $rates = [];
        foreach ($taxRates as $taxRate) {
            $rates[] = $this->factory->createListingItemTaxRate($taxRate['description'],
                                                                (float)$taxRate['rate'],
                                                                (int)$taxRate['priority']);
        }
        $rates     = $this->factory->createListingItemTaxRates(...$rates);
        $total     = $this->taxCalculator->calculate($rates);
        $formatted = $this->taxFormatter->format($total, $listingSettings);
        
        $title       = $taxClass['title'] ?? '';
        $description = $taxClass['description'] ?? '';
        
        return $this->factory->createListingItemTax($title,
                                                    $description,
                                                    $total,
                                                    $formatted,
                                                    $rates);
    }
    
    
    /**
     * Generates a cache key from the given arguments.
     *
     * @param int             $taxClassId
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    private function generateCacheKey(int $taxClassId, ListingSettings $listingSettings): string
    {
        return "$taxClassId-{$listingSettings->customerId()}-{$listingSettings->languageId()}";
    }
}
