<?php
/* --------------------------------------------------------------
   ListingItemDetailsCodes.php 2022-01-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\Collections;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDetailsCode;

/**
 * Class ListingItemDetailsCodes
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\Collections
 */
class ListingItemDetailsCodes
{
    /**
     * @var ListingItemDetailsCode[]
     */
    private array $codes;
    
    
    /**
     * ListingItemDetailsCodes constructor.
     *
     * @param ListingItemDetailsCode ...$codes
     */
    public function __construct(ListingItemDetailsCode ...$codes)
    {
        $this->codes = $codes;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        $cb = static function (ListingItemDetailsCode $code): array { return $code->toArray(); };
        
        return array_map($cb, $this->codes);
    }
}