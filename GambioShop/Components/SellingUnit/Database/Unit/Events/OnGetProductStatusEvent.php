<?php
/* --------------------------------------------------------------
  OnGetProductStatusEvent.php 2020-02-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Unit\Events;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductStatusEventInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\ProductInfoBuilderInterface;
use ProductDataInterface;

/**
 * Class OnGetProductStatusEvent
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events
 */
class OnGetProductStatusEvent implements OnGetProductStatusEventInterface
{
    /**
     * @var ProductInfoBuilderInterface
     */
    protected $builder;
    
    /**
     * @var ProductDataInterface
     */
    protected $product;
    
    /**
     * OnGetProductReleaseDateEvent constructor.
     *
     * @param ProductInfoBuilderInterface $builder
     * @param ProductDataInterface        $product
     */
    public function __construct(ProductInfoBuilderInterface $builder, ProductDataInterface $product)
    {
        $this->builder = $builder;
        $this->product = $product;
    }
    
    
    /**
     * @inheritDoc
     */
    public function builder() : ProductInfoBuilderInterface
    {
        return $this->builder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function product() : ProductDataInterface
    {
        return $this->product;
    }
}