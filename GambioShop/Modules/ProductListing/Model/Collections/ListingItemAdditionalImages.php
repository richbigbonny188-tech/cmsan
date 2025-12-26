<?php
/* --------------------------------------------------------------
  ListingItemAdditionalImages.php 2022-07-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\Collections;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemImage;

/**
 * Class ListingItemAdditionalImages
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\Collections
 */
class ListingItemAdditionalImages
{
    /**
     * @var ListingItemImage[]
     */
    private array $images;
    
    
    /**
     * ProductListingItemImages constructor.
     *
     * @param ListingItemImage ...$images
     */
    public function __construct(ListingItemImage ...$images)
    {
        $this->images = $images;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        $cb = static function (ListingItemImage $image): array { return $image->toArray(); };
        
        return array_map($cb, $this->images);
    }
}