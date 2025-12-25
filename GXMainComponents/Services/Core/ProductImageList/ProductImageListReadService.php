<?php
/* --------------------------------------------------------------
  ProductImageListReadService.php 2020-07-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList;

use Gambio\ProductImageList\Collections\ImageListsCollection;
use Gambio\ProductImageList\Exceptions\ImageDoesNotExistException;
use Gambio\ProductImageList\Image\Entities\Image;
use Gambio\ProductImageList\ImageList\Collections\ImageList;
use Gambio\ProductImageList\Interfaces\ProductImageListReadServiceInterface;
use Gambio\ProductImageList\ReadService\Interfaces\AttributeIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\CombiModelAndProductsIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\PropertiesCombisIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceRepositoryInterface;

/**
 * Class ProductImageListReadService
 * @package Gambio\ProductImageList
 */
class ProductImageListReadService implements ProductImageListReadServiceInterface
{
    
    /**
     * @var ReadServiceRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ProductImageListReadService constructor.
     *
     * @param ReadServiceRepositoryInterface $repository
     */
    public function __construct(ReadServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageLists(): ImageListsCollection
    {
        return $this->repository->getImageLists();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListById(int $id): ImageList
    {
        return $this->repository->getImageListById($id);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListByCombiModelAndProductsId(CombiModelAndProductsIdDtoInterface $dto): ImageList
    {
        $listId = $this->repository->getImageListIdByCombiModelAndProductsId($dto);
        
        return $this->repository->getImageListById($listId->value());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListByCombiId(PropertiesCombisIdDtoInterface $dto): ImageList
    {
        $listId = $this->repository->getImageListIdByCombiId($dto);
        
        return $this->repository->getImageListById($listId->value());
    }

    /**
     * @inheritDoc
     */
    public function getImageListByAttributeId(AttributeIdDtoInterface $dto): ImageList
    {
        $listId = $this->repository->getImageListIdByAttributeId($dto);

        return $this->repository->getImageListById($listId->value());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageById(int $id): Image
    {
        foreach ($this->getImageLists() as $list) {
    
            if ($list->count()) {
                
                foreach ($list as $image) {
                    
                    if ($image->id()->value() === $id) {
                        
                        return $image;
                    }
                }
            }
        }
        
        throw ImageDoesNotExistException::forId($id);
    }
}