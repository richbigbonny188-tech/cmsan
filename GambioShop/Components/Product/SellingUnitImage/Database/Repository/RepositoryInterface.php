<?php
/*--------------------------------------------------------------------------------------------------
    RepositoryInterface.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;

/**
 * Interface ProductImageRepositoryInterface
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Interfaces
 */
interface RepositoryInterface
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
     * @return SellingUnitImageCollectionInterface
     */
    public function getProductImages(ProductId $id, LanguageId $languageId): SellingUnitImageCollectionInterface;
}