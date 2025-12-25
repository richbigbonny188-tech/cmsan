<?php
/*--------------------------------------------------------------
   ImageDeleted.php 2021-06-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Events;

use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;

/**
 * Class ImageDeleted
 * @package Gambio\Admin\Modules\ImageList\Model\Events
 */
class ImageDeleted
{
    /**
     * @var ImageListId
     */
    private $imageLocalPath;
    
    /**
     * @var ImagePath
     */
    private $imageId;
    
    
    /**
     * ImageDeleted constructor.
     *
     * @param ImageListId $imageListId
     * @param ImagePath   $imageLocalPath
     */
    private function __construct(ImageListId $imageListId, ImagePath $imageLocalPath)
    {
        $this->imageLocalPath = $imageListId;
        $this->imageId        = $imageLocalPath;
    }
    
    
    /**
     * @param ImageListId $imageListId
     * @param ImagePath   $imageId
     *
     * @return ImageDeleted
     */
    public static function create(ImageListId $imageListId, ImagePath $imageId): ImageDeleted
    {
        return new self($imageListId, $imageId);
    }
    
    
    /**
     * @return ImageListId
     */
    public function imageLocalPath(): ImageListId
    {
        return $this->imageLocalPath;
    }
    
    
    /**
     * @return ImagePath
     */
    public function imageId(): ImagePath
    {
        return $this->imageId;
    }
}