<?php
/* --------------------------------------------------------------
  ReadServiceImageListsCollectionFactoryInterface.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Interfaces;

use Gambio\ProductImageList\Collections\ImageListsCollection;

/**
 * Interface ReadServiceImageListsCollectionFactoryInterface
 * @package Gambio\ProductImageList\ReadService\Interfaces
 */
interface ReadServiceImageListsCollectionFactoryInterface
{
    /**
     * @param ImageListDtoInterface[] $imageListDtos
     * @param ImageListImageDtoInterface[] $imageListImageDtos
     * @param ImageListImageTextDtoInterface[] $imageListImageTextDtos
     *
     * @return ImageListsCollection
     */
    public function createImageListCollection(
        array $imageListDtos,
        array $imageListImageDtos,
        array $imageListImageTextDtos
    ): ImageListsCollection;
}