<?php
/* --------------------------------------------------------------
  ProductImageDisplay.php 2023-06-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\DataOutput;

/**
 * Class ProductImageDisplay
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
class ProductImageDisplay implements DataOutput
{
    /**
     * @param string|null $url
     * @param string      $altText
     * @param int         $originalWidth
     * @param int         $originalHeight
     * @param int         $thumbnailWidth
     * @param int         $thumbnailHeight
     */
    public function __construct(
        private ?string $url,
        private string  $altText,
        private int     $originalWidth,
        private int     $originalHeight,
        private int     $thumbnailWidth,
        private int     $thumbnailHeight
    ) {
    }


    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'PRODUCTS_IMAGE'         => $this->url,
            'PRODUCTS_IMAGE_W'       => (string)$this->originalWidth,
            'PRODUCTS_IMAGE_H'       => (string)$this->originalHeight,
            'PRODUCTS_IMAGE_WIDTH'   => $this->thumbnailWidth,
            'PRODUCTS_IMAGE_PADDING' => $this->getThumbnailPadding(),
            'PRODUCTS_IMAGE_ALT'     => $this->altText,
        ];
    }


    /**
     * @return int|float
     */
    private function getThumbnailPadding(): int|float
    {
        return (($this->thumbnailHeight + 8) - $this->originalHeight) / 2;
    }
}