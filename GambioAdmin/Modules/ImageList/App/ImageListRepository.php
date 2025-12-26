<?php
/*--------------------------------------------------------------
   ImageListRepository.php 2021-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App;

use Gambio\Admin\Modules\ImageList\App\Data\ImageListMapper;
use Gambio\Admin\Modules\ImageList\App\Data\ImageListReader;
use Gambio\Admin\Modules\ImageList\App\Data\ImageListWriter;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageListIds;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageLists;
use Gambio\Admin\Modules\ImageList\Model\Events\ImageListCreated;
use Gambio\Admin\Modules\ImageList\Model\Events\ImageListDeleted;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListName;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\CreationOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ImageList\Services\ImageListOperationPermitter;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository as ImageListRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Event\EventDispatcher;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Class ImageListRepository
 * @package Gambio\Admin\Modules\ImageList\App
 */
class ImageListRepository extends AbstractEventDispatchingRepository implements ImageListRepositoryInterface
{
    /**
     * @var ImageListMapper
     */
    private $mapper;
    
    /**
     * @var ImageListReader
     */
    private $reader;
    
    /**
     * @var ImageListWriter
     */
    private $writer;
    
    /**
     * @var ImageListOperationPermitter[]
     */
    private $permitters = [];
    
    
    /**
     * ImageListRepository constructor.
     *
     * @param ImageListMapper $mapper
     * @param ImageListReader $reader
     * @param ImageListWriter $writer
     * @param EventDispatcher $dispatcher
     */
    public function __construct(
        ImageListMapper $mapper,
        ImageListReader $reader,
        ImageListWriter $writer,
        EventDispatcher $dispatcher
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
        $this->setEventDispatcher($dispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterImageLists(Filters $filters, Sorting $sorting, Pagination $pagination): ImageLists
    {
        $data = $this->reader->filterImageLists($filters, $sorting, $pagination);
        
        return $this->mapper->mapImageLists($data);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListsTotalCount(Filters $filters): int
    {
        return $this->reader->getImageListsTotalCount($filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListById(ImageListId $imageListId): ImageList
    {
        $data = $this->reader->getImageListById($imageListId);
        
        return $this->mapper->mapImageList($data);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllImageLists(): ImageLists
    {
        return $this->mapper->mapImageLists($this->reader->getAllImageLists());
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageList(ImageListName $imageListName): ImageListId
    {
        foreach ($this->permitters as $permitter) {
    
            if ($permitter->permitsCreations($imageListName->value()) === false) {
    
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $id = $this->writer->createImageList($imageListName);
        $id = $this->mapper->mapImageListId($id);
        
        $this->dispatchEvent(ImageListCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleImageLists(ImageListName ...$imageListNames): ImageListIds
    {
        $imageListNamesString = array_map(static function (ImageListName $name): string {
            return $name->value();
        }, $imageListNames);
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations(...$imageListNamesString) === false) {
                
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $ids = $this->writer->createMultipleImageLists(...$imageListNames);
        $ids = $this->mapper->mapImageListIds($ids);
    
        foreach ($ids as $id) {
            
            $this->dispatchEvent(ImageListCreated::create($id));
        }
        
        return $ids;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeImageLists(ImageList ...$imageLists): void
    {
        foreach ($this->permitters as $permitter) {
    
            if ($permitter->permitsStorages(...$imageLists) === false) {
                
                throw OperationHasNotBeenPermittedException::forStorageByPermitter($permitter);
            }
        }
        
        $this->writer->storeImageLists(...$imageLists);
    
        foreach ($imageLists as $imageList) {
            
            $this->dispatchEntityEvents($imageList);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteImageLists(ImageListId ...$imageListIds): void
    {
        foreach ($this->permitters as $permitter) {
            
            if ($permitter->permitsDeletions(...$imageListIds) === false) {
                
                throw OperationHasNotBeenPermittedException::forDeletetionByPermitter($permitter);
            }
        }
        
        $this->writer->deleteImageLists(...$imageListIds);
        
        foreach ($imageListIds as $imageListId) {
            
            $this->dispatchEvent(ImageListDeleted::create($imageListId));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(ImageListOperationPermitter $permitter): void
    {
        $this->permitters[get_class($permitter)] = $permitter;
    }
}