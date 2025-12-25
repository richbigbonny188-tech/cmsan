<?php
/* --------------------------------------------------------------
  NumberOfOrders.php 2020-02-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class NumberOfOrders
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class NumberOfOrders
{
    /**
     * @var float
     */
    protected $numberOfOrders = 0.0;
    
    
    /**
     * NumberOfOrders constructor.
     *
     * @param float $numberOfOrders
     */
    public function __construct(float $numberOfOrders)
    {
        $this->numberOfOrders = $numberOfOrders;
    }
    
    
    /**
     * @return float
     */
    public function value() : float
    {
        return $this->numberOfOrders;
    }
}