<?php
/* --------------------------------------------------------------
  ListingItemImage.php 2022-08-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemImage
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemImage
{
    private string $url;
    private string $alt;
    
    
    /**
     * ListingItemImage constructor.
     *
     * @param string $url
     * @param string $alt
     */
    public function __construct(string $url, string $alt)
    {
        
        $this->url = $url;
        $this->alt = $alt;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'alt' => $this->alt,
        ];
    }
}