<?php
/* --------------------------------------------------------------
  ListingItemShippingRangeBound.php 2022-06-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemShippingRangeBound
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemShippingRangeBound
{
    private int    $days;
    private string $name;
    private string $image;
    
    
    /**
     * @param int    $days
     * @param string $name
     * @param string $image
     */
    public function __construct(int $days, string $name, string $image)
    {
        $this->days  = $days;
        $this->name  = $name;
        $this->image = $image;
    }
    
    
    /**
     * @return int
     */
    public function days(): int
    {
        return $this->days;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return string
     */
    public function image(): string
    {
        return $this->image;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'days'  => $this->days(),
            'name'  => $this->name(),
            'image' => $this->image(),
        ];
    }
}