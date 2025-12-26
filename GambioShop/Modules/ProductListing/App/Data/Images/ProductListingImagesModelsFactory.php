<?php
/* --------------------------------------------------------------
  ProductListingImagesModelsFactory.php 2022-08-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Images;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemAdditionalImages;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImage;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImages;

/**
 * Class ProductListingImagesModelsFactory
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Images
 */
class ProductListingImagesModelsFactory
{
    public const PRODUCT_IMAGES_PATH = 'images/product_images/thumbnail_images/';
    
    
    /**
     * Creates ListingItemImages instance.
     *
     * @param ListingItemImage|null            $main
     * @param ListingItemAdditionalImages|null $additional
     *
     * @return ListingItemImages
     */
    public function createListingItemImages(
        ?ListingItemImage            $main,
        ?ListingItemAdditionalImages $additional
    ): ListingItemImages {
        return new ListingItemImages($main, $additional);
    }
    
    
    /**
     * Creates ListingItemAdditionalImages instance.
     *
     * @param ListingItemImage ...$images
     *
     * @return ListingItemAdditionalImages
     */
    public function createListingItemAdditionalImages(ListingItemImage ...$images): ListingItemAdditionalImages
    {
        return new ListingItemAdditionalImages(...$images);
    }
    
    
    /**
     * Creates ListingItemImage instance.
     * Appends product_images path to image url before creation.
     *
     * @param string $image
     * @param string $altText
     *
     * @return ListingItemImage
     */
    public function createListingItemImage(string $image, string $altText): ListingItemImage
    {
        return new ListingItemImage(self::PRODUCT_IMAGES_PATH . $image, $altText);
    }
}