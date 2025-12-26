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

namespace Gambio\Admin\Modules\ImageList\Services;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageListIds;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageLists;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListName;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\CreationOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\DeletionOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageListDoesNotExistException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\StorageOfImageListsFailedException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface ImageListRepository
 * @package Gambio\Admin\Modules\ImageList\Services
 */
interface ImageListRepository
{
    /**
     * Returns a filtered, sorted, paginated collection of image lists.
     *
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return ImageLists
     */
    public function filterImageLists(Filters $filters, Sorting $sorting, Pagination $pagination): ImageLists;
    
    
    /**
     * Returns the total count of filtered image lists.
     *
     * @param Filters $filters
     *
     * @return int
     */
    public function getImageListsTotalCount(Filters $filters): int;
    
    
    /**
     * Returns a specific image list based on the given image list ID.
     *
     * @param ImageListId $imageListId
     *
     * @return ImageList
     *
     * @throws ImageListDoesNotExistException
     */
    public function getImageListById(ImageListId $imageListId): ImageList;
    
    
    /**
     * Returns a collection of all image lists.
     *
     * @return ImageLists
     */
    public function getAllImageLists(): ImageLists;
    
    
    /**
     * Creates an empty image list.
     *
     * @param ImageListName $imageListName
     *
     * @return ImageListId
     * @throws CreationOfImageListsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createImageList(ImageListName $imageListName): ImageListId;
    
    
    /**
     * Creates multiple empty image list.
     *
     * @param ImageListName ...$imageListNames
     *
     * @return ImageListIds
     * @throws CreationOfImageListsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleImageLists(ImageListName ...$imageListNames): ImageListIds;
    
    
    /**
     * Stores one or more existing image lists.
     *
     * @param ImageList ...$imageLists
     *
     * @throws StorageOfImageListsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeImageLists(ImageList ...$imageLists): void;
    
    
    /**
     * Deletes one or more existing image lists.
     *
     * @param ImageListId ...$imageListIds
     *
     * @throws DeletionOfImageListsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteImageLists(ImageListId ...$imageListIds): void;
    
    /**
     * Registers an operation permitter, which can determine if a deletion operation is permitted.
     *
     * @param ImageListOperationPermitter $permitter
     */
    public function registerOperationPermitter(ImageListOperationPermitter $permitter): void;
}