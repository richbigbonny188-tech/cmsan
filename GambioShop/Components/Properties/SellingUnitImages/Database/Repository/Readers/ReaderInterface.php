<?php
/*--------------------------------------------------------------------
 ReaderInterface.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Readers;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\ImageListIsEmptyException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\PropertyDoesNotHaveAnImageListException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO\CombisIdDto;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO\ImageDto;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO\ImageDtoCollection;

/**
 * Interface ReaderInterface
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Readers
 */
interface ReaderInterface
{
    /**
     * @param CombisIdDto $dto
     * @param LanguageId $languageId
     *
     * @return ImageDto
     * @throws ImageListIsEmptyException
     * @throws PropertyDoesNotHaveAnImageListException
     */
    public function getMainImageListImage(CombisIdDto $dto, LanguageId $languageId): ImageDto;
    
    
    /**
     * @param CombisIdDto $dto
     * @param LanguageId $languageId
     *
     * @return ImageDtoCollection
     * @throws ImageListIsEmptyException
     * @throws PropertyDoesNotHaveAnImageListException
     */
    public function getImageListImages(CombisIdDto $dto, LanguageId $languageId): ImageDtoCollection;
}