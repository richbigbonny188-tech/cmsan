<?php
/*--------------------------------------------------------------------
 CombisIdIdentifierInterface.php 2020-2-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\ModifierCollectionDoesNotContainAnyPropertiesException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO\CombisIdDto;

/**
 * Interface CombisIdIdentifierInterface
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers
 */
interface CombisIdIdentifierInterface
{
    /**
     * @param ProductId $identification
     * @param ModifierIdentifierCollectionInterface $modifiers
     *
     * @return CombisIdDto
     * @throws ModifierCollectionDoesNotContainAnyPropertiesException
     */
    public function getCombisId(
        ProductId $identification,
        ModifierIdentifierCollectionInterface $modifiers
    ): CombisIdDto;
}