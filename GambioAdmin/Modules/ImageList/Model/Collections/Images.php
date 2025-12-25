<?php
/*--------------------------------------------------------------
   Images.php 2021-06-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ImageList\Model\Entities\Image;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageDoesNotExistException;
use IteratorAggregate;
use Traversable;

/**
 * Class Images
 * @package Gambio\Admin\Modules\ImageList\Model\Collections
 */
class Images implements IteratorAggregate
{
    /**
     * @var Image[]
     */
    private $images;
    
    
    /**
     * Images constructor.
     *
     * @param Image[] $images
     */
    private function __construct(array $images)
    {
        $this->images = [];
        
        foreach ($images as $image) {
            
            $this->images[$image->relativePath()] = $image;
        }
    }
    
    
    /**
     * @param Image ...$images
     *
     * @return Images
     */
    public static function create(Image ...$images): Images
    {
        return new self($images);
    }
    
    
    /**
     * @return Traversable|Image[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->images);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $images = array_map(static function (Image $image): array {
            return $image->toArray();
        },
            $this->images);
        
        return array_values($images);
    }
    
    
    /**
     * @param Image ...$images
     *
     * @return Images
     */
    public function with(Image ...$images): Images
    {
        $items = $this->images;
        
        foreach ($images as $image) {
            
            $items[$image->relativePath()] = $image;
        }
        
        return new self($items);
    }
    
    
    /**
     * @param ImagePath ...$imagePaths
     *
     * @return Images
     */
    public function without(ImagePath ...$imagePaths): Images
    {
        $items = $this->images;
        
        foreach ($imagePaths as $imagePath) {
            
            unset($items[$imagePath->relativePath()]);
        }
        
        return new self($items);
    }
    
    
    /**
     * @param ImagePath $localPath
     *
     * @return Image
     * @throws ImageDoesNotExistException
     */
    public function getByLocalPath(ImagePath $localPath): Image
    {
        if (isset($this->images[$localPath->relativePath()]) === false) {
            
            throw ImageDoesNotExistException::forImageLocalPath($localPath);
        }
        
        return $this->images[$localPath->relativePath()];
    }
    
    
    /**
     * @return int
     */
    public function getHighestSortValue(): int
    {
        $highest = 0;
        
        foreach ($this->getIterator() as $image) {
            
            if ($image->sortOrder() > $highest) {
                
                $highest = $image->sortOrder();
            }
        }
        
        return $highest;
    }
}