<?php
/*--------------------------------------------------------------
   ImageListWriteService.php 2021-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageListIds;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListName;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageAlreadyExistsException;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository as ImageListRepositoryInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;

/**
 * Class ImageListWriteService
 * @package Gambio\Admin\Modules\ImageList\App
 */
class ImageListWriteService implements ImageListWriteServiceInterface
{
    /**
     * @var ImageListRepositoryInterface
     */
    private $repository;
    
    /**
     * @var ImageListFactory
     */
    private $factory;
    
    
    /**
     * ImageListWriteService constructor.
     *
     * @param ImageListRepositoryInterface $repository
     * @param ImageListFactory             $factory
     */
    public function __construct(ImageListRepositoryInterface $repository, ImageListFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageList(ImageListName $imageListName): ImageListId
    {
        return $this->repository->createImageList($imageListName);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleImageLists(ImageListName ...$imageListNames): ImageListIds
    {
        return $this->repository->createMultipleImageLists(...$imageListNames);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeImageLists(ImageList ...$imageLists): void
    {
        $this->repository->storeImageLists(...$imageLists);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteImageLists(int ...$imageListsIds): void
    {
        $imageListsIds = array_map([$this->factory, 'createImageListId'], $imageListsIds);
        
        $this->repository->deleteImageLists(...$imageListsIds);
    }
}