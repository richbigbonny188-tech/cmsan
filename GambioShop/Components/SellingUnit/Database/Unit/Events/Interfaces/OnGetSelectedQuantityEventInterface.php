<?php
/*--------------------------------------------------------------------------------------------------
    OnGetSelectedQuantityEventInterface.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces;

use Exception;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\QuantityGraduation;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\ExceptionStacker;

interface OnGetSelectedQuantityEventInterface extends BasicSellingUnitEventInterface
{
    /**
     * @return SelectedQuantity
     */
    public function selectedQuantity() : QuantityInterface;
    
    
    /**
     * @param QuantityInterface $quantity
     */
    public function setSelectedQuantity(QuantityInterface $quantity) :void;
    
    
    /**
     * @return QuantityInterface
     */
    public function requestedQuantity() : QuantityInterface;

    /**
     * @return ExceptionStacker
     */
    public function exceptionStacker() : ExceptionStacker;

    /**
     * @return QuantityGraduation
     */
    public function quantityGraduation(): QuantityGraduation;

    /**
     * @return SellingUnitStockInterface
     */
    public function stock(): SellingUnitStockInterface;


    
}