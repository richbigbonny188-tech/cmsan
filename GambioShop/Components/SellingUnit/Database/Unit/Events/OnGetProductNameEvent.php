<?php
/*--------------------------------------------------------------------
 OnGetProductNameEvent.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Unit\Events;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductNameEventInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\ProductInfoBuilderInterface;
use ProductDataInterface;

/**
 * Class OnGetProductNameEvent
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events
 */
class OnGetProductNameEvent implements OnGetProductNameEventInterface
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
     * OnGetProductNameEvent constructor.
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
    public function builder(): ProductInfoBuilderInterface
    {
        return $this->builder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function product(): ProductDataInterface
    {
        return $this->product;
    }
}