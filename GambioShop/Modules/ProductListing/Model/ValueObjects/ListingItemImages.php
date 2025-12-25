<?php
/* --------------------------------------------------------------
  ListingItemImages.php 2022-08-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemAdditionalImages;

/**
 * Class ListingItemImages
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemImages
{
    private ?ListingItemImage            $mainImage;
    private ?ListingItemAdditionalImages $additionalImages;
    
    
    /**
     * ListingItemImages Constructor.
     *
     * @param ListingItemImage|null            $mainImage
     * @param ListingItemAdditionalImages|null $additionalImages
     */
    public function __construct(
        ?ListingItemImage            $mainImage,
        ?ListingItemAdditionalImages $additionalImages
    ) {
        
        $this->mainImage        = $mainImage;
        $this->additionalImages = $additionalImages;
    }
    
    
    /**
     * Returns serialized array of object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'main'       => $this->mainImage ? $this->mainImage->toArray() : [],
            'additional' => $this->additionalImages ? $this->additionalImages->toArray() : [],
        ];
    }
}