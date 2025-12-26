<?php
/*--------------------------------------------------------------------------------------------------
    OnCreateSellingUnitEventInterface.php 2020-02-13
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces;

use Gambio\Shop\SellingUnit\Core\Events\SellingUnitEventInterface;
use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\SellingUnitBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use PriceDataInterface;
use ProductDataInterface;

interface OnCreateSellingUnitEventInterface extends SellingUnitEventInterface
{
    /**
     * @return SellingUnitBuilderInterface
     */
    public function builder(): SellingUnitBuilderInterface;
    
    
    /**
     * @param ProductDataInterface $product
     *
     * @return mixed
     */
    public function setProduct(ProductDataInterface $product);
    
    
    /**
     * @param PriceDataInterface $price
     *
     * @return mixed
     */
    public function setXtcPrice(PriceDataInterface $price);
    
}