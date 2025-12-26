<?php
/* --------------------------------------------------------------
   ImageListDeleted.php 2021-06-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Events;


use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;

/**
 * Class ImageListDeleted
 * @package Gambio\Admin\Modules\ImageList\Model\Events
 */
class ImageListDeleted
{
    /**
     * @var ImageListId
     */
    private $imageListId;
    
    
    /**
     * ImageListDeleted constructor.
     *
     * @param ImageListId $imageListId
     */
    private function __construct(ImageListId $imageListId)
    {
        $this->imageListId = $imageListId;
    }
    
    
    /**
     * @param ImageListId $imageListId
     *
     * @return ImageListDeleted
     */
    public static function create(ImageListId $imageListId): ImageListDeleted
    {
        return new self($imageListId);
    }
    
    /**
     * @return ImageListId
     */
    public function imageListId(): ImageListId
    {
        return $this->imageListId;
    }
}