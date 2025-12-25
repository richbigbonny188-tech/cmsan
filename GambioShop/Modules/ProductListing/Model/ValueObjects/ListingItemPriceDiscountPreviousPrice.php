<?php
/* --------------------------------------------------------------
   ListingItemPriceDiscountPreviousPrice.php 2022-08-05
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
 * Class ListingItemPriceDiscountPreviousPrice
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPriceDiscountPreviousPrice
{
    private float  $value;
    private string $formatted;
    
    
    /**
     * ListingItemPriceDiscountPreviousPrice constructor.
     *
     * @param float  $value
     * @param string $formatted
     */
    public function __construct(float $value, string $formatted)
    {
        $this->value     = $value;
        $this->formatted = $formatted;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'value'     => $this->value,
            'formatted' => $this->formatted,
        ];
    }
}