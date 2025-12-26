<?php
/**
 * ProductImageListCreateServiceInterface.php 2020-1-22
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\ProductImageList\Interfaces;

use Gambio\ProductImageList\CreateService\Interfaces\ImageListImageDtoInterface;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\Entities\Image;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;

/**
 * Interface ProductImageListCreateServiceInterface
 * @package Gambio\ProductImageList\Interfaces
 */
interface ProductImageListCreateServiceInterface
{
    /**
     * @param string $listName
     */
    public function createImageList(string $listName): void;
    
    
    /**
     * @param ImageListImageDtoInterface $imageDto
     *
     * @return Id
     */
    public function createImage(ImageListImageDtoInterface $imageDto): Id;
    
    
    /**
     * @param TextCollection $titles
     * @param TextCollection $altTitles
     */
    public function createImageTexts(TextCollection $titles, TextCollection $altTitles): void;
}