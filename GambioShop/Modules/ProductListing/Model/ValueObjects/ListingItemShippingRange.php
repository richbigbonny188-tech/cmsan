<?php
/* --------------------------------------------------------------
  ListingItemShippingRange.php 2022-06-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemShippingRange
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemShippingRange
{
    private ListingItemShippingRangeBound $min;
    private ListingItemShippingRangeBound $max;
    
    
    public function __construct(ListingItemShippingRangeBound $min, ListingItemShippingRangeBound $max)
    {
        $this->min = $min;
        $this->max = $max;
    }
    
    
    /**
     * @return ListingItemShippingRangeBound
     */
    public function min(): ListingItemShippingRangeBound
    {
        return $this->min;
    }
    
    
    /**
     * @return ListingItemShippingRangeBound
     */
    public function max(): ListingItemShippingRangeBound
    {
        return $this->max;
    }
    
    
    public function toArray(): array
    {
        return [
            'low'  => $this->min()->toArray(),
            'high' => $this->max()->toArray(),
        ];
    }
    
}