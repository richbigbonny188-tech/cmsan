<?php
/* --------------------------------------------------------------
  ProductListingImagesRepository.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Images;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImage;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImages;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingImagesRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Images
 */
class ProductListingImagesRepository
{
    private ProductListingImagesReader        $reader;
    private ProductListingImagesModelsFactory $factory;
    
    
    /**
     * ProductListingImagesRepository constructor.
     *
     * @param ProductListingImagesReader        $reader
     * @param ProductListingImagesModelsFactory $factory
     */
    public function __construct(
        ProductListingImagesReader        $reader,
        ProductListingImagesModelsFactory $factory
    ) {
        $this->reader  = $reader;
        $this->factory = $factory;
    }
    
    
    /**
     * Returns listing item images on the products ID.
     *
     * @param ListingItemId   $listingItemId
     * @param ListingSettings $listingSettings
     *
     * @return ListingItemImages
     */
    public function getListingItemImages(
        ListingItemId   $listingItemId,
        ListingSettings $listingSettings
    ): ListingItemImages {
        $productId            = $listingItemId->asInt();
        $languageId           = $listingSettings->languageId();
        $mainImageData        = $this->reader->fetchMainImage($productId, $languageId);
        $additionalImagesData = $this->reader->fetchAdditionalImages($productId, $languageId);
        $mainImage            = !empty($mainImageData) ? $this->getImage($mainImageData) : null;
        $additionalImages     = !empty($additionalImagesData) ? $this->factory->createListingItemAdditionalImages(...
            array_map([$this, 'getImage'], $additionalImagesData)) : null;
        
        return $this->factory->createListingItemImages($mainImage, $additionalImages);
    }
    
    
    /**
     * Creates ListingItemImage from reader fetched data.
     *
     * @param array $rawData
     *
     * @return ListingItemImage
     */
    private function getImage(array $rawData): ListingItemImage
    {
        ['image' => $image, 'alt' => $alt] = $rawData;
        
        if (empty($alt)) {
            $alt = '';
        }
        
        return $this->factory->createListingItemImage($image, $alt);
    }
}