<?php
/* --------------------------------------------------------------
  ImageBuilder.php 2020-01-21
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\Builders;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\Entities\Image;
use Gambio\ProductImageList\Image\Exceptions\UnfinishedBuildException;
use Gambio\ProductImageList\Image\Interfaces\ImageBuilderInterface;
use Gambio\ProductImageList\Image\Interfaces\ImageInterface;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\Image\ValueObjects\SortOrder;
use Gambio\ProductImageList\Image\ValueObjects\WebFilePath;

/**
 * Class ImageBuilder
 * @package Gambio\ProductImageList\Image\Builders
 */
class ImageBuilder implements ImageBuilderInterface
{
    /**
     * @var Id
     */
    protected $id;
    
    /**
     * @var LocalFilePath
     */
    protected $localFilePath;
    
    /**
     * @var WebFilePath
     */
    protected $webFilePath;
    
    /**
     * @var SortOrder
     */
    protected $sortOrder;
    
    /**
     * @var TextCollection
     */
    protected $titles;
    /**
     * @var TextCollection
     */
    protected $altTitles;
    
    
    /**
     * @inheritDoc
     */
    public static function create(): ImageBuilderInterface
    {
        return new static;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withId(Id $id): ImageBuilderInterface
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withLocalFilePath(LocalFilePath $localFilePath): ImageBuilderInterface
    {
        $this->localFilePath = $localFilePath;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withWebFilePath(WebFilePath $webFilePath): ImageBuilderInterface
    {
        $this->webFilePath = $webFilePath;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withSortOrder(SortOrder $sortOrder): ImageBuilderInterface
    {
        $this->sortOrder = $sortOrder;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): ImageInterface
    {
        if (!isset($this->id, $this->webFilePath, $this->localFilePath, $this->sortOrder, $this->titles, $this->altTitles)) {
            
            throw new UnfinishedBuildException('Not all necessary properties are set for a ' . Image::class);
        }
    
        return new Image($this->id,
                         $this->localFilePath,
                         $this->webFilePath,
                         $this->sortOrder,
                         $this->titles,
                         $this->altTitles);
    }
    
    
    public function reset(): void
    {
        $this->id = $this->webFilePath = $this->localFilePath = $this->sortOrder = $this->titles = $this->altTitles = null;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withTitles(TextCollection $titles): ImageBuilderInterface
    {
        $this->titles = $titles;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withAltTitles(TextCollection $altTitles): ImageBuilderInterface
    {
        $this->altTitles = $altTitles;
        
        return $this;
    }
}