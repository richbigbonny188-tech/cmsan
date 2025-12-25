<?php
/*--------------------------------------------------------------
   ImageAdded.php 2021-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Events;

use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\NewImage;

/**
 * Class ImageAdded
 * @package Gambio\Admin\Modules\ImageList\Model\Events
 */
class ImageAdded
{
    /**
     * @var ImageListId
     */
    private $imageListId;
    
    /**
     * @var NewImage
     */
    private $newImage;
    
    
    /**
     * ImageAdded constructor.
     *
     * @param ImageListId $imageListId
     * @param NewImage    $newImage
     */
    private function __construct(ImageListId $imageListId, NewImage $newImage)
    {
        $this->imageListId = $imageListId;
        $this->newImage    = $newImage;
    }
    
    
    /**
     * @param ImageListId $imageListId
     * @param NewImage    $newImage
     *
     * @return ImageAdded
     */
    public static function create(ImageListId $imageListId, NewImage $newImage): ImageAdded
    {
        return new self($imageListId, $newImage);
    }
    
    
    /**
     * @return ImageListId
     */
    public function imageListId(): ImageListId
    {
        return $this->imageListId;
    }
    
    
    /**
     * @return NewImage
     */
    public function newImage(): NewImage
    {
        return $this->newImage;
    }
}