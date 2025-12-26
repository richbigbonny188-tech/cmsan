<?php
/*--------------------------------------------------------------------------------------------------
    ReadRepositoryInterface.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\Attributes\ProductModifiers\Database\ValueObjects\AttributeModifierIdentifier;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;

interface ReadRepositoryInterface
{
    /**
     * @param AttributeModifierIdentifier $attributeId
     * @param ProductId                   $productId
     * @param LanguageId                  $languageId
     *
     * @return SellingUnitImageCollectionInterface
     */
    public function getAttributeOptionImagesByProductId(
        AttributeModifierIdentifier $attributeId,
        ProductId $productId,
        LanguageId $languageId
    ) : SellingUnitImageCollectionInterface;
}