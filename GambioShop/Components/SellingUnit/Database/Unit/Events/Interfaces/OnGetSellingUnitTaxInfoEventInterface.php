<?php
/*--------------------------------------------------------------------
 OnGetSellingUnitTaxInfoEventInterface.php 2020-2-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\SellingUnitBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\Entities\TaxInfoInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TaxInfo;
use PriceDataInterface;
use ProductDataInterface;

/**
 * Interface OnGetSellingUnitTaxInfoEventInterface
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces
 */
interface OnGetSellingUnitTaxInfoEventInterface
{
    
    /**
     * @return ProductDataInterface
     */
    public function product(): ProductDataInterface;
    
    /**
     * @return PriceDataInterface|null
     */
    public function xtcPrice(): ?PriceDataInterface;
    
    /**
     * @return TaxInfo
     */
    public function taxInfo(): TaxInfo;
    
    /**
     * @param TaxInfo $taxInfo
     */
    public function setTaxInfo(TaxInfo $taxInfo): void;
}