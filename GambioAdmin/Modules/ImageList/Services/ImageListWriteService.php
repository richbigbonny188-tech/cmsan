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

namespace Gambio\Admin\Modules\ImageList\Services;

use Exception;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageListIds;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListName;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\CreationOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\DeletionOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageAlreadyExistsException;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\StorageOfImageListsFailedException;

/**
 * Interface ImageListWriteService
 * @package Gambio\Admin\Modules\ImageList\Services
 */
interface ImageListWriteService
{
    /**
     * Creates an empty image list.
     *
     * @param ImageListName $imageListName
     *
     * @return ImageListId
     */
    public function createImageList(ImageListName $imageListName): ImageListId;
    
    
    /**
     * Creates multiple empty image list.
     *
     * @param ImageListName ...$imageListNames
     *
     * @return ImageListIds
     * @throws CreationOfImageListsFailedException
     */
    public function createMultipleImageLists(ImageListName ...$imageListNames): ImageListIds;
    
    
    /**
     * Stores one or more existing image lists.
     *
     * @param ImageList ...$imageLists
     *
     * @throws StorageOfImageListsFailedException
     */
    public function storeImageLists(ImageList ...$imageLists): void;
    
    
    /**
     * Deletes one or more existing image lists.
     *
     * @param int ...$imageListsIds
     *
     * @throws DeletionOfImageListsFailedException
     */
    public function deleteImageLists(int ...$imageListsIds): void;
}