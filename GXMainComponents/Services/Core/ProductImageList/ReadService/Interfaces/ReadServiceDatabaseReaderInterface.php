<?php
/* --------------------------------------------------------------
  ReadServiceDatabaseReaderInterface.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Interfaces;


use Gambio\ProductImageList\ReadService\Exceptions\AttributeDoesNotHaveAListException;
use Gambio\ProductImageList\ReadService\Exceptions\CombinationDoesNotHaveAListException;

/**
 * Interface ReadServiceDatabaseReaderInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface ReadServiceDatabaseReaderInterface
{
    /**
     * @return ImageListDtoInterface[]
     */
    public function getImageLists(): array;
    
    
    /**
     * @return ImageListImageDtoInterface[]
     */
    public function getImages(): array;
    
    
    /**
     * @return ImageListImageTextDtoInterface[]
     */
    public function getImageTexts(): array;
    
    
    /**
     * @param int $id
     *
     * @return ImageListDtoInterface[]
     */
    public function getImageListById(int $id): array;
    
    
    /**
     * @param int $listId
     *
     * @return ImageListImageDtoInterface[]
     */
    public function getImagesByListId(int $listId): array;
    
    
    /**
     * @param int $listId
     *
     * @return ImageListImageTextDtoInterface[]
     */
    public function getImageTextsByListId(int $listId): array;
    
    
    /**
     * @param CombiModelAndProductsIdDtoInterface $dto
     *
     * @return int
     *
     * @throws CombinationDoesNotHaveAListException
     */
    public function getImageListIdByCombiModelAndProductsId(CombiModelAndProductsIdDtoInterface $dto): int;
    
    
    /**
     * @param PropertiesCombisIdDtoInterface $dto
     *
     * @return int
     * @throws CombinationDoesNotHaveAListException
     */
    public function getImageListIdByCombiId(PropertiesCombisIdDtoInterface $dto): int;


    /**
     * @param AttributeIdDtoInterface $dto
     *
     * @return int
     * @throws AttributeDoesNotHaveAListException
     */
    public function getImageListIdByAttributeId(AttributeIdDtoInterface $dto): int;
}