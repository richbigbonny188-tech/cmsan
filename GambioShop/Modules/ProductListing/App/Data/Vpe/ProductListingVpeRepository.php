<?php
/*--------------------------------------------------------------
   ProductListingVpeRepository.php 2023-06-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Vpe;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingVpeRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Vpe
 */
class ProductListingVpeRepository
{
    /**
     * @param ProductListingVpeCalculator      $calculator
     * @param ProductListingVpeNumberFormatter $formatter
     * @param ProductListingVpeTextProvider    $textProvider
     */
    public function __construct(
        private ProductListingVpeCalculator $calculator,
        private ProductListingVpeNumberFormatter $formatter,
        private ProductListingVpeTextProvider $textProvider
    ) {
    }
    
    
    /**
     * @param ListingSettings     $listingSettings
     * @param ListingItemPrice    $price
     * @param ListingItemCurrency $currency
     * @param string              $vpeUnit
     * @param float               $vpeValue
     *
     * @return string
     */
    public function getFormatted(
        ListingSettings $listingSettings,
        ListingItemPrice $price,
        ListingItemCurrency $currency,
        string $vpeUnit,
        float $vpeValue
    ): string {
        $perUnit = $this->calculator->calculatePerUnitValue($price, $vpeValue);
        $perUnit = $this->formatter->formatFloat($perUnit, $currency);
        $per     = $this->textProvider->getPerPhrase($listingSettings);
        
        return $perUnit . $per . $vpeUnit;
    }
}