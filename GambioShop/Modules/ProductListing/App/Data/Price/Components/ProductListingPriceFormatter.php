<?php
/* --------------------------------------------------------------
   ProductListingPriceFormatter.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemPriceValue;

/**
 * Class ProductListingPriceFormatter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceFormatter
{
    private ProductListingPriceFormatterUtility $formatUtility;
    
    
    /**
     * ProductListingPriceFormatter constructor.
     *
     * @param ProductListingPriceFormatterUtility $formatUtility
     */
    public function __construct(ProductListingPriceFormatterUtility $formatUtility)
    {
        $this->formatUtility = $formatUtility;
    }
    
    
    /**
     * Formats the price value into a readable string, including currency information.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     *
     * @return string
     */
    public function format(ListingItemPriceValue $priceValue, ListingItemCurrency $currency): string
    {
        $formattedPrice = $this->formatUtility->formatPriceValue($priceValue, $currency);
        
        return $this->formatUtility->addCurrencySymbols($formattedPrice, $currency);
    }
}