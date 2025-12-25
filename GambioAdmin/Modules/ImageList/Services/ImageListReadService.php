<?php
/*--------------------------------------------------------------
   ImageListReadService.php 2021-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageLists;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageListDoesNotExistException;

/**
 * Interface ImageListReadService
 * @package Gambio\Admin\Modules\ImageList\Services
 */
interface ImageListReadService
{
    /**
     * Returns a specific image list based on the given option ID.
     *
     * @param int $imageListId
     *
     * @return ImageList
     *
     * @throws ImageListDoesNotExistException
     */
    public function getImageListById(int $imageListId): ImageList;
    
    
    /**
     * Returns a collection of all image lists.
     *
     * @return ImageLists
     */
    public function getAllImageLists(): ImageLists;
}