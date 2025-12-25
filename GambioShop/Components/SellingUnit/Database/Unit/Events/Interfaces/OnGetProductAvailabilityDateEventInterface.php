<?php
/* --------------------------------------------------------------
  OnGetProductAvailabilityDateEventInterface.php 2020-02-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces;

use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\ProductInfoBuilderInterface;
use ProductDataInterface;

/**
 * Interface OnGetProductAvailabilityDateEventInterface
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces
 */
interface OnGetProductAvailabilityDateEventInterface
{
    /**
     * @return ProductInfoBuilderInterface
     */
    public function builder() : ProductInfoBuilderInterface;
    
    
    /**
     * @return ProductDataInterface
     */
    public function product() : ProductDataInterface;
    
}