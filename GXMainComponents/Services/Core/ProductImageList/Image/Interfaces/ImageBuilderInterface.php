<?php
/* --------------------------------------------------------------
  ImageBuilderInterface.php 2020-01-21
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\Interfaces;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\Exceptions\UnfinishedBuildException;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\Image\ValueObjects\SortOrder;
use Gambio\ProductImageList\Image\ValueObjects\WebFilePath;

/**
 * Interface ImageBuilderInterface
 * @package Gambio\ProductImageList\Image\Interfaces
 */
interface ImageBuilderInterface
{
    /**
     * @return ImageBuilderInterface
     */
    public static function create(): ImageBuilderInterface;
    
    
    /**
     * @param Id $id
     *
     * @return ImageBuilderInterface
     */
    public function withId(Id $id): ImageBuilderInterface;
    
    
    /**
     * @param LocalFilePath $localFilePath
     *
     * @return ImageBuilderInterface
     */
    public function withLocalFilePath(LocalFilePath $localFilePath): ImageBuilderInterface;
    
    
    /**
     * @param WebFilePath $webFilePath
     *
     * @return ImageBuilderInterface
     */
    public function withWebFilePath(WebFilePath $webFilePath): ImageBuilderInterface;
    
    
    /**
     * @param SortOrder $sortOrder
     *
     * @return ImageBuilderInterface
     */
    public function withSortOrder(SortOrder $sortOrder): ImageBuilderInterface;
    
    
    /**
     * @param TextCollection $titles
     *
     * @return ImageBuilderInterface
     */
    public function withTitles(TextCollection $titles): ImageBuilderInterface;
    
    
    /**
     * @param TextCollection $altTitles
     *
     * @return ImageBuilderInterface
     */
    public function withAltTitles(TextCollection $altTitles): ImageBuilderInterface;
    
    /**
     * @return ImageInterface
     * @throws UnfinishedBuildException
     */
    public function build(): ImageInterface;
    
    
    public function reset(): void;
}