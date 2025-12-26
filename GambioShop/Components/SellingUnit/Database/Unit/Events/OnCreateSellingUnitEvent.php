<?php
/*--------------------------------------------------------------------------------------------------
    OnCreateSellingUnitEvent.php 2020-3-3
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Database\Unit\Events;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnCreateSellingUnitEventInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\SellingUnitBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\SellingUnitBuilder;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use ProductDataInterface;
use PriceDataInterface;

/**
 * Class OnCreateSellingUnitEvent
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events
 */
class OnCreateSellingUnitEvent implements OnCreateSellingUnitEventInterface
{
    /**
     * @var SellingUnitBuilder
     */
    protected $builder;
    
    /**
     * @var ProductDataInterface
     */
    protected $product;
    /**
     * @var \xtcPrice_ORIGIN
     */
    protected $xtcPrice;
    /**
     * @var SellingUnitId
     */
    private $id;
    
    
    /**
     * OnSellingUnitCreateEvent constructor.
     *
     * @param SellingUnitId                         $id
     * @param                                       $product
     * @param null                                  $xtcPrice
     * @param QuantityInterface|null                 $quantity
     */
    public function __construct(
        SellingUnitId $id,
        $product = null,
        $xtcPrice = null,
        QuantityInterface $quantity = null
    ) {
        
        $this->id       = $id;
        $this->product  = $product;
        $this->xtcPrice = $xtcPrice;
        $this->builder  = new SellingUnitBuilder;
        
        if ($quantity !== null) {
            
            $this->builder->withRequestedQuantity($quantity);
        }
    }
    
    
    /**
     * @return ProductDataInterface
     */
    public function product(): ?ProductDataInterface
    {
        return $this->product;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setProduct(ProductDataInterface $product)
    {
        $this->product = $product;
    }
    
    
    /**
     * @return SellingUnitId
     */
    public function id(): SellingUnitId
    {
        return $this->id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function xtcPrice(): ?PriceDataInterface
    {
        return $this->xtcPrice;
    }
    
    
    /**
     * @inheritDoc
     */
    public function builder(): SellingUnitBuilderInterface
    {
        return $this->builder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setXtcPrice(PriceDataInterface $price)
    {
        $this->xtcPrice = $price;
    }
}