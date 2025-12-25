<?php
/*
 * --------------------------------------------------------------
 *   ListingItemManufacturer.php 2022-01-19
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemManufacturer
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemManufacturer
{
    private string  $name;
    private string  $url;
    private ?string $image;
    
    
    /**
     * @param string      $name
     * @param string      $url
     * @param string|null $image
     */
    public function __construct(string $name, string $url, ?string $image)
    {
        $this->name  = $name;
        $this->url   = $url;
        $this->image = $image;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'url'  => $this->url,
        ];
        if ($this->image) {
            $data['image'] = $this->image;
        }
        
        return $data;
    }
}