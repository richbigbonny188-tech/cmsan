<?php
/*--------------------------------------------------------------
   ImageListNameUpdated.php 2021-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Events;

use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;

/**
 * Class ImageListNameUpdated
 * @package Gambio\Admin\Modules\ImageList\Model\Events
 */
class ImageListsNameUpdated
{
    /**
     * @var ImageListId
     */
    private $imageListId;
    
    /**
     * @var string
     */
    private $imageListName;
    
    
    /**
     * ImageListNameUpdated constructor.
     *
     * @param ImageListId $imageListId
     * @param string      $imageListName
     */
    private function __construct(ImageListId $imageListId, string $imageListName)
    {
        $this->imageListId   = $imageListId;
        $this->imageListName = $imageListName;
    }
    
    
    /**
     * @param ImageListId $imageListId
     * @param string      $imageListName
     *
     * @return ImageListsNameUpdated
     */
    public static function create(ImageListId $imageListId, string $imageListName): ImageListsNameUpdated
    {
        return new self($imageListId, $imageListName);
    }
    
    
    /**
     * @return ImageListId
     */
    public function imageListId(): ImageListId
    {
        return $this->imageListId;
    }
    
    
    /**
     * @return string
     */
    public function imageListName(): string
    {
        return $this->imageListName;
    }
}