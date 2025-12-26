<?php
/* --------------------------------------------------------------
   ProductListingPriceFormatterUtility.php 2022-08-10
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
 * Class ProductListingPriceFormatterUtility
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceFormatterUtility
{
    /**
     * Formats the price value based on currency information.
     *
     * The formatted price is returned as string, with currency defined decimal and thousands places
     * and also rounded to the amount of decimal places.
     *
     * @param ListingItemPriceValue $priceValue
     * @param ListingItemCurrency   $currency
     *
     * @return string
     */
    public function formatPriceValue(ListingItemPriceValue $priceValue, ListingItemCurrency $currency): string
    {
        return number_format($priceValue->value(),
                             $currency->decimalPlaces(),
                             $currency->decimalSeparator(),
                             $currency->thousandsSeparator());
    }
    
    
    /**
     * Adds currency symbols to the formatted price and takes care to trim unnecessary whitespaces.
     *
     * @param string              $formattedPrice
     * @param ListingItemCurrency $currency
     *
     * @return string
     */
    public function addCurrencySymbols(string $formattedPrice, ListingItemCurrency $currency): string
    {
        $left   = trim($currency->symbolLeft());
        $right  = trim($currency->symbolRight());
        $result = "$left $formattedPrice $right";
        
        return trim($result);
    }
}