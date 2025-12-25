<?php
/*--------------------------------------------------------------------
 SellingUnitEventTrait.php 2020-2-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\Shop\SellingUnit\Core\Events;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use PriceDataInterface;
use product_ORIGIN;
use ProductDataInterface;

/**
 * Trait SellingUnitEventTrait
 * @package Gambio\Shop\SellingUnit\Core\Events
 */
trait SellingUnitEventTrait
{
    /**
     * @var ProductDataInterface
     */
    protected $product;
    /**
     * @var SellingUnitId
     */
    private $id;
    /**
     * @var \xtcPrice_ORIGIN
     */
    protected $xtcPrice;
    
    
    /**
     * SellingUnitEventTrait constructor.
     *
     * @param SellingUnitId                         $id
     * @param                                       $product
     * @param                                       $xtcPrice
     */
    public function __construct(
        SellingUnitId $id,
        ProductDataInterface $product = null,
        ?PriceDataInterface $xtcPrice = null
    ) {
        
        $this->id       = $id;
        $this->product  = $product;
        $this->xtcPrice = $xtcPrice;
    }
    
    
    /**
     * @return ProductDataInterface
     */
    public function product(): ProductDataInterface
    {
        return $this->product;
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
}