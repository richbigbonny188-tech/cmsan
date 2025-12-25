<?php
/*--------------------------------------------------------------------------------------------------
    SellingUnitBuilderInterface.php 2020-3-3
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\Builders\Interfaces;

use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use PriceDataInterface;
use ProductDataInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

interface SellingUnitBuilderInterface
{
    /**
     * @param ProductDataInterface $product
     *
     * @return SellingUnitBuilderInterface
     */
    public function withProduct(ProductDataInterface $product): SellingUnitBuilderInterface;
    
    
    /**
     * @param PriceDataInterface $xtcPrice
     *
     * @return SellingUnitBuilderInterface
     */
    public function withXtcPrice(PriceDataInterface $xtcPrice): SellingUnitBuilderInterface;
    
    
    /**
     * @param SellingUnitId $id
     *
     * @return SellingUnitBuilderInterface
     */
    public function withId(SellingUnitId $id): SellingUnitBuilderInterface;
    
    
    /**
     * @param EventDispatcherInterface $dispatcher
     *
     * @return SellingUnitBuilderInterface
     */
    public function withDispatcher(EventDispatcherInterface $dispatcher): SellingUnitBuilderInterface;
    
    
    /**
     * @return SellingUnitInterface
     */
    public function build(): SellingUnitInterface;
    
    
    /**
     * @param QuantityInterface $quantity
     *
     * @return mixed
     */
    public function withRequestedQuantity(QuantityInterface $quantity);
    
}