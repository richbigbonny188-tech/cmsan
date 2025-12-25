<?php
/* --------------------------------------------------------------
  Image.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\Entities;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\Interfaces\ImageInterface;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\Image\ValueObjects\SortOrder;
use Gambio\ProductImageList\Image\ValueObjects\WebFilePath;
use JsonSerializable;

/**
 * Class Image
 * @package Gambio\ProductImageList\Image\Entities
 */
class Image implements ImageInterface, JsonSerializable
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
     * Image constructor.
     *
     * @param Id             $id
     * @param LocalFilePath  $localFilePath
     * @param WebFilePath    $webFilePath
     * @param SortOrder      $sortOrder
     * @param TextCollection $titles
     * @param TextCollection $altTitles
     */
    public function __construct(
        Id $id,
        LocalFilePath $localFilePath,
        WebFilePath $webFilePath,
        SortOrder $sortOrder,
        TextCollection $titles,
        TextCollection $altTitles
    ) {
        $this->id            = $id;
        $this->localFilePath = $localFilePath;
        $this->webFilePath   = $webFilePath;
        $this->sortOrder     = $sortOrder;
        $this->titles        = $titles;
        $this->altTitles     = $altTitles;
    }
    
    
    /**
     * @inheritDoc
     */
    public function id(): Id
    {
        return $this->id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function localFilePath(): LocalFilePath
    {
        return $this->localFilePath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function webFilePath(): WebFilePath
    {
        return $this->webFilePath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function sortOrder(): SortOrder
    {
        return $this->sortOrder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function titles(): TextCollection
    {
        return $this->titles;
    }
    
    
    /**
     * @inheritDoc
     */
    public function altTitles(): TextCollection
    {
        return $this->altTitles;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (object)[
            'id'            => $this->id(),
            'webFilePath'   => $this->webFilePath(),
            'sortOrder'     => $this->sortOrder(),
            'titles'        => $this->titles(),
            'altTitles'     => $this->altTitles()
        ];
    }
}