<?php
/* --------------------------------------------------------------
   ListingItemDetails.php 2022-01-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemDetailsCodes;

/**
 * Class ListingItemDetails
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemDetails
{
    private string $name;
    
    private string $description;
    
    private string $shortDescription;
    
    private string $model;
    
    private ListingItemDetailsCodes $codes;
    
    
    /**
     * ListingItemDetails constructor.
     *
     * @param string                  $name
     * @param string                  $description
     * @param string                  $shortDescription
     * @param string                  $model
     * @param ListingItemDetailsCodes $codes
     */
    public function __construct(
        string                  $name,
        string                  $description,
        string                  $shortDescription,
        string                  $model,
        ListingItemDetailsCodes $codes
    ) {
        
        $this->name             = $name;
        $this->description      = $description;
        $this->shortDescription = $shortDescription;
        $this->model            = $model;
        $this->codes            = $codes;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'             => $this->name,
            'description'      => $this->description,
            'shortDescription' => $this->shortDescription,
            'model'            => $this->model,
            'codes'            => $this->codes->toArray(),
        ];
    }
}