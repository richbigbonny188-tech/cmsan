<?php
/*--------------------------------------------------------------------------------------------------
    ScopedQuantityInterface.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces;


use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;

interface ScopedQuantityInterface
{
    /**
     * @return ReserveScopeInterface
     */
    public function scope(): ReserveScopeInterface;
}