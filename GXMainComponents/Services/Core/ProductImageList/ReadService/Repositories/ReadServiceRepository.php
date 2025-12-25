<?php
/* --------------------------------------------------------------
  ReadServiceRepository.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Repositories;

use Gambio\ProductImageList\Collections\ImageListsCollection;
use Gambio\ProductImageList\ImageList\Collections\ImageList;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\ReadService\Interfaces\AttributeIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\CombiModelAndProductsIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\PropertiesCombisIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceDatabaseReaderInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceImageListsCollectionFactoryInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceRepositoryInterface;

/**
 * Class ReadServiceRepository
 * @package Gambio\ProductImageList\ReadService\Repositories
 */
class ReadServiceRepository implements ReadServiceRepositoryInterface
{
    /**
     * @var ReadServiceDatabaseReaderInterface
     */
    protected $reader;
    
    /**
     * @var ReadServiceImageListsCollectionFactoryInterface
     */
    protected $factory;
    
    
    /**
     * ReadServiceRepository constructor.
     *
     * @param ReadServiceDatabaseReaderInterface              $reader
     * @param ReadServiceImageListsCollectionFactoryInterface $factory
     */
    public function __construct(
        ReadServiceDatabaseReaderInterface $reader,
        ReadServiceImageListsCollectionFactoryInterface $factory
    ) {
        $this->reader  = $reader;
        $this->factory = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageLists(): ImageListsCollection
    {
        return $this->factory->createImageListCollection($this->reader->getImageLists(),
                                                         $this->reader->getImages(),
                                                         $this->reader->getImageTexts());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListById(int $id): ImageList
    {
        $result = $this->factory->createImageListCollection($this->reader->getImageListById($id),
                                                            $this->reader->getImagesByListId($id),
                                                            $this->reader->getImageTextsByListId($id));
        
        return $result[0];
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListIdByCombiModelAndProductsId(CombiModelAndProductsIdDtoInterface $dto): ListId
    {
        $listId = $this->reader->getImageListIdByCombiModelAndProductsId($dto);
        
        return new ListId($listId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListIdByCombiId(PropertiesCombisIdDtoInterface $dto): ListId
    {
        return new ListId($this->reader->getImageListIdByCombiId($dto));
    }

    /**
     * @inheritDoc
     */
    public function getImageListIdByAttributeId(AttributeIdDtoInterface $dto): ListId
    {
        return new ListId($this->reader->getImageListIdByAttributeId($dto));
    }
}