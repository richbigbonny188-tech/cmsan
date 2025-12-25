<?php
/* --------------------------------------------------------------
   ListingItemVariantId.php 2022-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemVariantId
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemVariantId
{
    private int $id;
    
    
    /**
     * ListingItemVariantId constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @return int
     */
    public function asInt(): int
    {
        return $this->id;
    }
}