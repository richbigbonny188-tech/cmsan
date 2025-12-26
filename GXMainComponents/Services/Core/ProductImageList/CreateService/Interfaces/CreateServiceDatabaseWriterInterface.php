<?php
/* --------------------------------------------------------------
  CreateServiceDatabaseWriterInterface.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\CreateService\Interfaces;

use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;

/**
 * Interface CreateServiceDatabaseWriterInterface
 * @package Gambio\ProductImageList\CreateService\Interfaces
 */
interface CreateServiceDatabaseWriterInterface
{
    /**
     * @param string $listName
     */
    public function createImageList(string $listName): void;
    
    
    /**
     * @param ImageListImageDtoInterface $image
     *
     * @return int
     */
    public function createImage(ImageListImageDtoInterface $image): int;
    
    /**
     * @param TextCollection $titles
     * @param TextCollection $altTitles
     */
    public function createImageTexts(TextCollection $titles, TextCollection $altTitles): void;
}