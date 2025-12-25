<?php
/*--------------------------------------------------------------
   ImageList.php 2021-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model;

use Gambio\Admin\Modules\ImageList\Model\Collections\Images;
use Gambio\Admin\Modules\ImageList\Model\Collections\NewImages;
use Gambio\Admin\Modules\ImageList\Model\Entities\Image;
use Gambio\Admin\Modules\ImageList\Model\Events\ImageDeleted;
use Gambio\Admin\Modules\ImageList\Model\Events\ImageUpdated;
use Gambio\Admin\Modules\ImageList\Model\Events\ImageListsNameUpdated;
use Gambio\Admin\Modules\ImageList\Model\Events\ImageAdded;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListName;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\NewImage;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;
use Webmozart\Assert\Assert;

/**
 * Class ImageList
 * @package Gambio\Admin\Modules\ImageList\Model
 */
class ImageList extends AbstractEventRaisingEntity
{
    /**
     * @var ImageListId
     */
    private $imageListId;
    
    /**
     * @var ImageListName
     */
    private $imageListName;
    
    /**
     * @var Images
     */
    private $images;
    
    /**
     * @var NewImages
     */
    private $newImages;
    
    
    /**
     * ImageList constructor.
     *
     * @param ImageListId   $imageListId
     * @param ImageListName $imageListName
     * @param Images        $images
     * @param NewImages     $newImages
     */
    private function __construct(
        ImageListId $imageListId,
        ImageListName $imageListName,
        Images $images,
        NewImages $newImages
    ) {
        $this->imageListId   = $imageListId;
        $this->imageListName = $imageListName;
        $this->images        = $images;
        $this->newImages     = $newImages;
    }
    
    
    /**
     * @param ImageListId   $imageListId
     * @param ImageListName $imageListName
     * @param Images        $images
     * @param NewImages     $newImages
     *
     * @return ImageList
     */
    public static function create(
        ImageListId $imageListId,
        ImageListName $imageListName,
        Images $images,
        NewImages $newImages
    ): ImageList {
        return new self($imageListId, $imageListName, $images, $newImages);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->imageListId->value();
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->imageListName->value();
    }
    
    
    /**
     * @return Images
     */
    public function images(): Images
    {
        return $this->images;
    }
    
    
    /**
     * @return NewImages
     */
    public function newImages(): NewImages
    {
        return $this->newImages;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'     => $this->id(),
            'name'   => $this->name(),
            'images' => $this->images()->toArray()
        ];
    }
    
    
    /**
     * @param ImageListName $imageListName
     */
    public function changeName(ImageListName $imageListName): void
    {
        $this->imageListName = $imageListName;
        $this->raiseEvent(ImageListsNameUpdated::create($this->imageListId, $imageListName->value()));
    }
    
    
    /**
     * @param Image ...$images
     */
    public function changeImages(Image ...$images): void
    {
        $this->images = $this->images()->with(...$images);
        
        foreach ($images as $image) {
            
            $this->raiseEvent(ImageUpdated::create($this->imageListId, $image));
        }
    }
    
    
    /**
     * @param NewImage ...$newImages
     */
    public function addNewImages(NewImage ...$newImages): void
    {
        $this->newImages = $this->newImages()->with(...$newImages);
        
        foreach ($newImages as $newImage) {
            
            $this->raiseEvent(ImageAdded::create($this->imageListId, $newImage));
        }
    }
    
    
    /**
     * @param ImagePath ...$imagePaths
     */
    public function removeImage(ImagePath ...$imagePaths): void
    {
        foreach ($imagePaths as $imagePath) {
            
            $this->raiseEvent(ImageDeleted::create($this->imageListId, $imagePath));
        }
        
        $this->images = $this->images()->without(...$imagePaths);
    }
}