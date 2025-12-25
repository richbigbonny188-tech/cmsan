<?php
/* --------------------------------------------------------------
   ListingItemStock.php 2022-05-10
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
 * Class ListingItemStock
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemStock
{
    private float   $quantity;
    private float   $interval;
    private float   $orderMinQuantity;
    private ?string $unit;
    private ?string $availability;
    
    
    /**
     * ListingItemStock constructor.
     *
     * @param float       $quantity
     * @param float       $interval
     * @param float       $orderMinQuantity
     * @param string|null $unit
     * @param string|null $availability
     */
    public function __construct(
        float   $quantity,
        float   $interval,
        float   $orderMinQuantity,
        ?string $unit,
        ?string $availability
    ) {
        $this->quantity         = $quantity;
        $this->interval         = $interval;
        $this->orderMinQuantity = $orderMinQuantity;
        $this->unit             = $unit;
        $this->availability     = $availability;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'quantity'         => $this->quantity,
            'interval'         => $this->interval,
            'orderMinQuantity' => $this->orderMinQuantity,
            'unit'             => $this->unit,
            'availability'     => $this->availability,
        ];
    }
}