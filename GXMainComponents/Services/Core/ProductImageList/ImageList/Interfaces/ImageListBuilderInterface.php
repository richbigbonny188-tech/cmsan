<?php
/* --------------------------------------------------------------
  ImageListBuilderInterface.php 2020-01-21
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ImageList\Interfaces;

use Gambio\ProductImageList\Image\Interfaces\ImageInterface;
use Gambio\ProductImageList\ImageList\Builders\ImageListBuilder;
use Gambio\ProductImageList\ImageList\Collections\ImageList;
use Gambio\ProductImageList\ImageList\Exceptions\UnfinishedBuildException;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\ImageList\ValueObjects\ListName;

/**
 * Interface ImageListBuilderInterface
 * @package Gambio\ProductImageList\ImageList\Interfaces
 */
interface ImageListBuilderInterface
{
    /**
     * @return ImageListBuilderInterface
     */
    public static function create(): ImageListBuilderInterface;
    
    
    /**
     * @param ListId $listId
     *
     * @return ImageListBuilderInterface
     */
    public function withListId(ListId $listId): ImageListBuilderInterface;
    
    
    /**
     * @param ImageInterface $image
     *
     * @return ImageListBuilderInterface
     */
    public function withImage(ImageInterface $image): ImageListBuilderInterface;
    
    
    /**
     * @param ListName $listName
     *
     * @return ImageListBuilder
     */
    public function withListName(ListName $listName): ImageListBuilder;
    
    /**
     * @return ImageList
     * @throws UnfinishedBuildException
     */
    public function build(): ImageList;
    
    
    public function reset(): void;
}