<?php
/* --------------------------------------------------------------
   ListingCategories.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\Collections;

use Gambio\Admin\Modules\Configuration\Model\ValueObjects\ListingCategory;
use JsonSerializable;

/**
 * Class ListingCategories
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Collections
 */
class ListingCategories implements JsonSerializable
{
    /**
     * @var ListingCategory[]
     */
    private $listingCategories;
    
    
    /**
     * ListingCategories constructor.
     *
     * @param ListingCategory[] $listingCategories
     */
    private function __construct(ListingCategory ...$listingCategories)
    {
        $this->listingCategories = $listingCategories;
    }
    
    
    /**
     * @param ListingCategory ...$listingCategories
     *
     * @return ListingCategories
     */
    public static function create(ListingCategory ...$listingCategories): ListingCategories
    {
        return new self(...$listingCategories);
    }
    
    
    /**
     * @return array Returns array of objects, that matches the ListingCategory schema from "configuration.schema.json".
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->listingCategories;
    }
}