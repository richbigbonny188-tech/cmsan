<?php
/*--------------------------------------------------------------------------------------------------
    ScopedQuantity.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;


use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollection;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\ReserveScopeInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\ScopedQuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\ExceptionStacker;

class ScopedQuantity extends AbstractQuantity implements ScopedQuantityInterface, ExceptionStacker
{
    /**
     * @var SellingUnitInterface[]
     */
    protected $scope;

    /**
     * ScopedQuantity constructor.
     * @param float $quantity
     * @param string $measureUnit
     * @param ReserveScopeInterface $scope
     */
    public function __construct(float $quantity, string $measureUnit, ReserveScopeInterface $scope)
    {
        parent::__construct($quantity, $measureUnit);
        $this->scope = $scope;
    }


    public function scope(): ReserveScopeInterface
    {
        return $this->scope;
    }

}