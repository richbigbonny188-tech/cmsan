<?php
/* --------------------------------------------------------------
   ProductListingTaxCalculator.php 2022-05-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Tax;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemTaxRates;

/**
 * Class ProductListingTaxCalculator
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxCalculator
{
    private const PRECISION = 4;
    
    
    /**
     * Tax rate calculation.
     * Calculates the rate value of given listing item tax rates.
     *
     * Rates with same priority are summed and then used to calculate a multiplier.
     * By dividing the summed tax rate through 100 and adding one, we get a percentage value as multiplier.
     * Afterwards, the multiplier is subtracted by one and multiplied by 100 in order to get the final tax rate.
     * Finally, the tax rate is rounded with a precision of 4.
     *
     * @param ListingItemTaxRates $rates
     *
     * @return float
     */
    public function calculate(ListingItemTaxRates $rates): float
    {
        $rateValues = [];
        foreach ($rates as $rate) {
            $prio              = $rate->priority();
            $rateValues[$prio] = array_key_exists($prio, $rateValues) ? $rateValues[$prio]
                                                                        + $rate->rate() : $rate->rate();
        }
        
        $multiplier = 1.0;
        foreach ($rateValues as $rateValue) {
            $percentageDecimal = $rateValue / 100;
            $percentage        = 1.0 + $percentageDecimal;
            
            $multiplier *= $percentage;
        }
        $rate = ($multiplier - 1.0) * 100;
        
        return round($rate, self::PRECISION);
    }
}