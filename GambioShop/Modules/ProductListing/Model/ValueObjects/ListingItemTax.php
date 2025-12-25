<?php
/* --------------------------------------------------------------
   ListingItemTax.php 2022-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemTaxRates;

/**
 * Class ListingItemTax
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemTax
{
    private string              $title;
    private string              $description;
    private float               $total;
    private string              $formatted;
    private ListingItemTaxRates $rates;
    
    
    /**
     * ListingItemTax constructor.
     *
     * @param string              $title
     * @param string              $description
     * @param float               $total
     * @param string              $formatted
     * @param ListingItemTaxRates $rates
     */
    public function __construct(
        string              $title,
        string              $description,
        float               $total,
        string              $formatted,
        ListingItemTaxRates $rates
    ) {
        $this->title       = $title;
        $this->description = $description;
        $this->total       = $total;
        $this->formatted   = $formatted;
        $this->rates       = $rates;
    }
    
    
    /**
     * Tax total value, based on calculated tax rates.
     *
     * @return float
     */
    public function total(): float
    {
        return $this->total;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title'       => $this->title,
            'description' => $this->description,
            'total'       => $this->total,
            'formatted'   => $this->formatted,
            'rates'       => $this->rates->toArray(),
        ];
    }
}