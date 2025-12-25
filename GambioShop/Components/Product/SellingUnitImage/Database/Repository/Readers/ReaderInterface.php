<?php
/*--------------------------------------------------------------------------------------------------
    ReaderInterface.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository\Readers;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\SellingUnitImage\Database\Exceptions\ProductDoesNotHaveAnImageException;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\ImageDto;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\ImageDtoCollection;
use Gambio\Shop\Product\ValueObjects\ProductId;

/**
 * Interface ProductImageReaderInterface
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Interfaces
 */
interface ReaderInterface
{
    /**
     * @param ProductId  $id
     *
     * @param LanguageId $languageId
     *
     * @return ImageDto
     * @throws ProductDoesNotHaveAnImageException
     */
    public function getMainProductImage(ProductId $id, LanguageId $languageId): ImageDto;
    
    
    /**
     * @param ProductId  $id
     * @param LanguageId $languageId
     *
     * @return ImageDtoCollection
     * @throws ProductDoesNotHaveAnImageException
     */
    public function getProductImages(ProductId $id, LanguageId $languageId): ImageDtoCollection;
}