<?php
/* --------------------------------------------------------------
   ProductListingPriceHandler.php 2023-05-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price;

use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceCalculator;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFormatter;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemPriceInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPrice;

/**
 * Class ProductListingPriceHandler
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceHandler
{
    /**
     * ProductListingPriceHandler constructor.
     *
     * @param ProductListingPriceCalculator  $calculator
     * @param ProductListingPriceFactory   $factory
     * @param ProductListingPriceFormatter $formatter
     */
    public function __construct(
        private ProductListingPriceCalculator $calculator,
        private ProductListingPriceFactory $factory,
        private ProductListingPriceFormatter $formatter
    ) {
    }
    
    
    /**
     * Returns the normal base price without any extras.
     *
     * @param ListingItemPriceInformation $priceInformation
     * @param ListingItemCurrency         $currency
     *
     * @return ListingItemPrice
     */
    public function getNormalPrice(
        ListingItemPriceInformation $priceInformation,
        ListingItemCurrency         $currency
    ): ListingItemPrice {
        
        $priceValue = $this->factory->createPriceValue($priceInformation->basePrice());
        $finalPrice = $this->calculator->addTax($priceValue, $priceInformation->taxRate());
        $formatted  = $this->formatter->format($finalPrice, $currency);
        
        return $this->factory->createPrice($finalPrice, $formatted);
    }
}