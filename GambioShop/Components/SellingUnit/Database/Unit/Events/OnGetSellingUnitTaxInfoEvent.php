<?php
/*--------------------------------------------------------------------
 OnGetSellingUnitTaxInfoEvent.php 2020-2-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\Shop\SellingUnit\Database\Unit\Events;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Core\Events\SellingUnitEventTrait;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitTaxInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\SellingUnitBuilder;
use Gambio\Shop\SellingUnit\Unit\Builders\SellingUnitBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TaxInfo;
use PHPUnit\Util\RegularExpressionTest;
use PriceDataInterface;

/**
 * Class OnGetSellingUnitTaxInfoEvent
 * @package Gambio\Shop\SellingUnit\Database\Unit\Events
 */
class OnGetSellingUnitTaxInfoEvent implements OnGetSellingUnitTaxInfoEventInterface
{
    use SellingUnitEventTrait;
    
    /**
     * @var TaxInfo
     */
    protected $taxInfo;
    
    
    /**
     * OnSellingUnitCreateEvent constructor.
     *
     * @param                                       $product
     * @param PriceDataInterface                    $xtcPrice
     */
    public function __construct(
        $product,
        PriceDataInterface $xtcPrice
    ) {
        
        $this->product  = $product;
        $this->xtcPrice = $xtcPrice;
    }
    
    
    /**
     * @inheritDoc
     */
    public function taxInfo(): TaxInfo
    {
        return $this->taxInfo;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setTaxInfo(TaxInfo $taxInfo): void
    {
        $this->taxInfo = $taxInfo;
    }
}