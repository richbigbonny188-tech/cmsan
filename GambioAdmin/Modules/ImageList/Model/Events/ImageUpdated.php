<?php
/*--------------------------------------------------------------
   ImageUpdated.php 2021-06-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Events;

use Gambio\Admin\Modules\ImageList\Model\Entities\Image;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;

/**
 * Class ImageUpdated
 * @package Gambio\Admin\Modules\ImageList\Model\Events
 */
class ImageUpdated
{
    /**
     * @var ImageListId
     */
    private $imageListId;
    
    /**
     * @var Image
     */
    private $image;
    
    
    /**
     * ImageUpdated constructor.
     *
     * @param ImageListId $imageListId
     * @param Image       $image
     */
    private function __construct(ImageListId $imageListId, Image $image)
    {
        $this->imageListId = $imageListId;
        $this->image       = $image;
    }
    
    
    /**
     * @param ImageListId $imageListId
     * @param Image       $image
     *
     * @return ImageUpdated
     */
    public static function create(ImageListId $imageListId, Image $image): ImageUpdated
    {
        return new self($imageListId, $image);
    }
    
    
    /**
     * @return ImageListId
     */
    public function imageListId(): ImageListId
    {
        return $this->imageListId;
    }
    
    
    /**
     * @return Image
     */
    public function image(): Image
    {
        return $this->image;
    }
}