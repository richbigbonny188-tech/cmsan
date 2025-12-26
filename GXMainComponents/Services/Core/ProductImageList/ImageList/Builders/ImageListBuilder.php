<?php
/* --------------------------------------------------------------
  ImageListBuilder.php 2020-01-21
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ImageList\Builders;

use Gambio\ProductImageList\Image\Interfaces\ImageInterface;
use Gambio\ProductImageList\ImageList\Collections\ImageList;
use Gambio\ProductImageList\ImageList\Exceptions\UnfinishedBuildException;
use Gambio\ProductImageList\ImageList\Interfaces\ImageListBuilderInterface;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\ImageList\ValueObjects\ListName;

/**
 * Class ImageListBuilder
 * @package Gambio\ProductImageList\ImageList\Builders
 */
class ImageListBuilder implements ImageListBuilderInterface
{
    /**
     * @var ListId
     */
    protected $listId;
    
    /**
     * @var ImageInterface[]
     */
    protected $imageList = [];
    /**
     * @var ListName
     */
    protected $listName;
    
    
    /**
     * @inheritDoc
     */
    public static function create(): ImageListBuilderInterface
    {
        return new static;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withListId(ListId $listId): ImageListBuilderInterface
    {
        $this->listId = $listId;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withImage(ImageInterface $image): ImageListBuilderInterface
    {
        $this->imageList[] = $image;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): ImageList
    {
        if ($this->listName === null) {
            
            throw new UnfinishedBuildException(static::class . ' needs to have a ListName');
        }
        
        return new ImageList($this->listName, $this->imageList, $this->listId);
    }
    
    
    public function reset(): void
    {
        $this->listId    = $this->listName = null;
        $this->imageList = [];
        
    }
    
    
    /**
     * @inheritDoc
     */
    public function withListName(ListName $listName): ImageListBuilder
    {
        $this->listName = $listName;
        
        return $this;
    }
}