<?php
/*--------------------------------------------------------------------------------------------------
    GroupReaderCompositeInterface.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\Readers\Interfaces;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTOCollectionInterface;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use IdType;

/**
 * Interface GroupReaderCompositeInterface
 * @package Gambio\Shop\ProductModifiers\Database\Core\Readers\Interfaces
 */
interface GroupReaderCompositeInterface
{
    /**
     * @param ProductId $id
     * @param IdType    $languageId
     *
     * @return mixed
     */
    public function getGroupsByProduct(
        ProductId $id,
        LanguageId $languageId
    ): GroupDTOCollectionInterface;

    /**
     * @param SellingUnitId $id
     * @param LanguageId $languageId
     *
     * @return GroupDTOCollectionInterface
     */
    public function getGroupsBySellingUnit(
        SellingUnitId $id,
        LanguageId $languageId
    ): GroupDTOCollectionInterface;
}