<?php
/*------------------------------------------------------------------------------
 SellingUnitInterface.php 2022-11-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Shop\SellingUnit\Unit;

use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Presentation\SellingUnitPresenterInterface;
use Gambio\Shop\SellingUnit\Unit\Entities\Interfaces\ProductInfoInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\AbstractQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Ean;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Model;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\QuantityGraduation;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\ShippingInfo;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TaxInfo;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TotalFinalPrice;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Vpe;
use PriceDataInterface;

interface SellingUnitInterface
{
    /**
     * @return SellingUnitId
     */
    public function id(): SellingUnitId;
    
    
    /**
     * @return ProductInfoInterface
     */
    public function productInfo(): ProductInfoInterface;
    
    
    /**
     * @return Entities\Price
     */
    public function price(): Entities\Price;
    
    
    /**
     * @return TaxInfo
     */
    public function taxInfo(): TaxInfo;
    
    
    /**
     * @return Vpe
     */
    public function vpe(): ?Vpe;
    
    
    /**
     * @return ValueObjects\Weight
     */
    public function weight(): ?ValueObjects\Weight;
    
    
    /**
     * @return ShippingInfo
     */
    public function shipping(): ShippingInfo;
    
    
    /**
     * @return SellingUnitImageCollectionInterface
     */
    public function images(): SellingUnitImageCollectionInterface;
    
    
    /**
     * @return Model
     */
    public function model(): Model;
    
    
    /**
     * @return Ean
     */
    public function ean(): Ean;
    
    
    /**
     * @return AbstractQuantity
     */
    public function selectedQuantity(): AbstractQuantity;
    
    
    /**
     * @return QuantityGraduation
     */
    public function quantityGraduation(): QuantityGraduation;
    
    
    /**
     * @return SellingUnitStockInterface
     */
    public function stock(): SellingUnitStockInterface;
    
    
    /**
     * @return PriceDataInterface
     */
    public function xtcPrice(): PriceDataInterface;
    
    
    /**
     * @return SellingUnitPresenterInterface
     */
    public function presenter(): SellingUnitPresenterInterface;
    
    
    /**
     * @return TotalFinalPrice
     */
    public function totalFinalPrice(): TotalFinalPrice;
}