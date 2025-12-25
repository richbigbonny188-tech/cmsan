<?php
/**
 * ProductImageListReadServiceInterface.php 2020-07-28
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\ProductImageList\Interfaces;

use Gambio\ProductImageList\Collections\ImageListsCollection;
use Gambio\ProductImageList\Exceptions\ImageDoesNotExistException;
use Gambio\ProductImageList\Image\Entities\Image;
use Gambio\ProductImageList\ImageList\Collections\ImageList;
use Gambio\ProductImageList\ReadService\Exceptions\AttributeDoesNotHaveAListException;
use Gambio\ProductImageList\ReadService\Exceptions\CombinationDoesNotHaveAListException;
use Gambio\ProductImageList\ReadService\Interfaces\AttributeIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\CombiModelAndProductsIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\PropertiesCombisIdDtoInterface;

/**
 * Interface ProductImageListReadServiceInterface
 * @package Gambio\ProductImageList\Interfaces
 */
interface ProductImageListReadServiceInterface
{
    /**
     * @return ImageListsCollection
     */
    public function getImageLists(): ImageListsCollection;
    
    
    /**
     * @param int $id
     *
     * @return ImageList
     */
    public function getImageListById(int $id): ImageList;
    
    
    /**
     * @param CombiModelAndProductsIdDtoInterface $dto
     *
     * @return ImageList
     * @throws CombinationDoesNotHaveAListException
     */
    public function getImageListByCombiModelAndProductsId(CombiModelAndProductsIdDtoInterface $dto): ImageList;
    
    
    /**
     * @param PropertiesCombisIdDtoInterface $dto
     *
     * @return ImageList
     * @throws CombinationDoesNotHaveAListException
     */
    public function getImageListByCombiId(PropertiesCombisIdDtoInterface $dto): ImageList;


    /**
     * @param AttributeIdDtoInterface $dto
     *
     * @return ImageList
     * @throws AttributeDoesNotHaveAListException
     */
    public function getImageListByAttributeId(AttributeIdDtoInterface $dto): ImageList;
    
    
    /**
     * @param int $id
     *
     * @return Image
     * @throws ImageDoesNotExistException
     */
    public function getImageById(int $id): Image;
}