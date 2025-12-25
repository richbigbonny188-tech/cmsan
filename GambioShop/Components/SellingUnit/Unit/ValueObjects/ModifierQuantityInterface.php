<?php
/*--------------------------------------------------------------------------------------------------
    ModifierQuantityInterface.php 2020-3-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;


use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;

interface ModifierQuantityInterface extends QuantityInterface
{
    /**
     * @return ModifierIdentifierCollectionInterface
     */
    public function linkedModifiers(): ModifierIdentifierCollectionInterface;
}