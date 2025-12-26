<?php
/*--------------------------------------------------------------------------------------------------
    SellinhUnitEventInterface.php 2020-02-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\SellingUnit\Core\Events;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use PriceDataInterface;
use ProductDataInterface;

interface SellingUnitEventInterface
{
    /**
     * @return SellingUnitId
     */
    public function id(): SellingUnitId;
    
    
    /**
     * @return PriceDataInterface|null
     */
    public function xtcPrice(): ?PriceDataInterface;
    
    
    /**
     * @return ProductDataInterface|null
     */
    public function product(): ?ProductDataInterface;
    
    
}