<?php
/*--------------------------------------------------------------------------------------------------
    ReadServiceInterface.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Service;

use Gambio\ProductImageList\Image\Interfaces\ImageInterface;
use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\Properties\ProductModifiers\Database\ValueObjects\PropertyModifierIdentifier;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;

/**
 * Interface ProductImageServiceInterface
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Interfaces
 */
interface ReadServiceInterface
{
    /**
     * @param ProductId  $id
     *
     * @param LanguageId $languageId
     *
     * @return SellingUnitImageInterface|null
     */
    public function mainProductImage(ProductId $id, LanguageId $languageId): ?SellingUnitImageInterface;
    
    
    /**
     * @param ProductId  $id
     * @param LanguageId $languageId
     *
     * @return SellingUnitImageInterface|null
     */
    public function getProductImages(ProductId $id, LanguageId $languageId): ?SellingUnitImageCollectionInterface;
}