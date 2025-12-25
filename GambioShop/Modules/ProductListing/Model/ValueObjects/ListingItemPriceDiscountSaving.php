<?php
/* --------------------------------------------------------------
   ListingItemPriceDiscountSaving.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemPriceDiscountSaving
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPriceDiscountSaving
{
    private float  $percentage;
    private string $formatted;
    
    
    /**
     * ListingItemPriceDiscountSaving constructor.
     *
     * @param float  $percentage
     * @param string $formatted
     */
    public function __construct(float $percentage, string $formatted)
    {
        $this->percentage = $percentage;
        $this->formatted  = $formatted;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'percentage' => $this->percentage,
            'formatted'  => $this->formatted,
        ];
    }
}