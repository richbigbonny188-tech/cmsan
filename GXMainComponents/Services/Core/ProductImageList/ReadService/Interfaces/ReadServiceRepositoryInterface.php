<?php
/* --------------------------------------------------------------
  ReadServiceRepositoryInterface.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Interfaces;

use Gambio\ProductImageList\Collections\ImageListsCollection;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\ImageList\Collections\ImageList;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\ReadService\Exceptions\AttributeDoesNotHaveAListException;
use Gambio\ProductImageList\ReadService\Exceptions\CombinationDoesNotHaveAListException;

/**
 * Interface ReadServiceRepositoryInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface ReadServiceRepositoryInterface
{
    /**
     * @return ImageListsCollection
     */
    public function getImageLists() : ImageListsCollection;
    
    /**
     * @param int $id
     *
     * @return ImageList
     */
    public function getImageListById(int $id): ImageList;
    
    
    /**
     * @param CombiModelAndProductsIdDtoInterface $dto
     *
     * @return ListId
     * @throws CombinationDoesNotHaveAListException
     */
    public function getImageListIdByCombiModelAndProductsId(CombiModelAndProductsIdDtoInterface $dto): ListId;
    
    
    /**
     * @param PropertiesCombisIdDtoInterface $dto
     *
     * @return ListId
     * @throws CombinationDoesNotHaveAListException
     */
    public function getImageListIdByCombiId(PropertiesCombisIdDtoInterface $dto): ListId;


    /**
     * @param AttributeIdDtoInterface $dto
     *
     * @return ListId
     * @throws AttributeDoesNotHaveAListException
     */
    public function getImageListIdByAttributeId(AttributeIdDtoInterface $dto): ListId;
}