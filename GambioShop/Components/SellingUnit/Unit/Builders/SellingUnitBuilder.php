<?php
/*--------------------------------------------------------------------------------------------------
    SellingUnitBuilder.php 2020-3-3
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Unit\Builders;

use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\SellingUnitBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\SellingUnit;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use PriceDataInterface;
use product_ORIGIN;
use ProductDataInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use xtcPrice_ORIGIN;

/**
 * Class SellingUnitBuilder
 * @package Gambio\Shop\SellingUnit\Unit\Builders
 */
class SellingUnitBuilder implements SellingUnitBuilderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    /**
     * @var SellingUnitId
     */
    protected $id;
    /**
     * @var ProductDataInterface
     */
    protected $product;
    /**
     * @var PriceDataInterface
     */
    protected $xtcPrice;
    /**
     * @var SelectedQuantity
     */
    protected $requestedQuantity;
    
    
    /**
     * @inheritDoc
     */
    public function withProduct(ProductDataInterface $product): SellingUnitBuilderInterface
    {
        $this->product = $product;
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withDispatcher(EventDispatcherInterface $dispatcher): SellingUnitBuilderInterface
    {
        $this->dispatcher = $dispatcher;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): SellingUnitInterface
    {
        return new SellingUnit($this->id, $this->dispatcher, $this->requestedQuantity, $this->product, $this->xtcPrice);
    }
    
    
    /**
     * @inheritDoc
     */
    public function withXtcPrice(PriceDataInterface $xtcPrice): SellingUnitBuilderInterface
    {
        $this->xtcPrice = $xtcPrice;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withId(SellingUnitId $id): SellingUnitBuilderInterface
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withRequestedQuantity(QuantityInterface $quantity)
    {
        $this->requestedQuantity = $quantity;
        return $this;
    }
}