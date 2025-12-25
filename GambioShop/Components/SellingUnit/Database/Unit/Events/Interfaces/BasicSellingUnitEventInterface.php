<?php
/*--------------------------------------------------------------------------------------------------
    BasicSellingUnitEventInterface.php 2020-02-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use ProductDataInterface;

interface BasicSellingUnitEventInterface
{
    /**
     * @return SellingUnitId
     */
    public function id(): SellingUnitId;
    
    
    /**
     * @return ProductDataInterface
     */
    public function product() : ProductDataInterface;
    
}