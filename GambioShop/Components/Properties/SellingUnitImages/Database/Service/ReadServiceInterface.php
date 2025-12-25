<?php
/*--------------------------------------------------------------------
 ReadServiceInterface.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Service;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\ImageListIsEmptyException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\PropertyDoesNotHaveAnImageListException;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;

/**
 * Interface ReadServiceInterface
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Service
 */
interface ReadServiceInterface
{
    /**
     * @param ProductId                             $identification
     * @param ModifierIdentifierCollectionInterface $identifierCollection
     * @param LanguageId                            $languageId
     *
     * @return SellingUnitImageInterface
     * @throws ImageListIsEmptyException
     * @throws PropertyDoesNotHaveAnImageListException
     */
    public function getMainImageListImage(
        ProductId $identification,
        ModifierIdentifierCollectionInterface $identifierCollection,
        LanguageId $languageId
    ): SellingUnitImageInterface;
    
    
    /**
     * @param ProductId                             $identification
     * @param ModifierIdentifierCollectionInterface $identifierCollection
     * @param LanguageId                            $languageId
     *
     * @return SellingUnitImageCollectionInterface
     * @throws ImageListIsEmptyException
     * @throws PropertyDoesNotHaveAnImageListException
     */
    public function getImageListImages(
        ProductId $identification,
        ModifierIdentifierCollectionInterface $identifierCollection,
        LanguageId $languageId
    ): SellingUnitImageCollectionInterface;
}