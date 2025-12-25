<?php
/*--------------------------------------------------------------------
 OnGetProductUrlEvent.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Unit\Events;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductUrlEventInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\ProductInfoBuilderInterface;
use ProductDataInterface;

/**
 * Class OnGetProductUrlEvent
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events
 */
class OnGetProductUrlEvent implements OnGetProductUrlEventInterface
{
    /**
     * @var ProductInfoBuilderInterface
     */
    protected $builder;
    
    /**
     * @var ProductDataInterface
     */
    protected $productData;
    
    /**
     * @var ProductId
     */
    protected $productId;
    
    
    /**
     * OnGetProductUrlEvent constructor.
     *
     * @param ProductInfoBuilderInterface $builder
     * @param ProductDataInterface        $productData
     * @param ProductId                   $productId
     */
    public function __construct(
        ProductInfoBuilderInterface $builder,
        ProductDataInterface $productData,
        ProductId $productId
    ) {
        $this->builder = $builder;
        $this->productData = $productData;
        $this->productId = $productId;
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
        return $this->productData;
    }
    
    
    /**
     * @inheritDoc
     */
    public function productId(): ProductId
    {
        return $this->productId;
    }
}