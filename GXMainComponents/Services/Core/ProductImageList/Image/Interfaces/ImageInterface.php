<?php
/* --------------------------------------------------------------
  ImageInterface.php 2020-01-21
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\Interfaces;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\Image\ValueObjects\SortOrder;
use Gambio\ProductImageList\Image\ValueObjects\WebFilePath;

/**
 * Interface ImageInterface
 * @package Gambio\ProductImageList\Image\Interfaces
 */
interface ImageInterface
{
    /**
     * @return Id
     */
    public function id(): Id;
    
    
    /**
     * @return LocalFilePath
     */
    public function localFilePath(): LocalFilePath;
    
    
    /**
     * @return WebFilePath
     */
    public function webFilePath(): WebFilePath;
    
    
    /**
     * @return SortOrder
     */
    public function sortOrder(): SortOrder;
    
    
    /**
     * @return TextCollection
     */
    public function titles(): TextCollection;
    
    
    /**
     * @return TextCollection
     */
    public function altTitles(): TextCollection;
}