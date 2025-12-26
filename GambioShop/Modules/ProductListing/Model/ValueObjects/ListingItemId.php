<?php
/* --------------------------------------------------------------
   ListingItemId.php 2022-01-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use InvalidArgumentException;

/**
 * Class ListingItemId
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemId
{
    private int $productId;
    
    
    /**
     * ListingItemId constructor.
     *
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        if ($productId <= 0) {
            throw new InvalidArgumentException("Product id ($productId) must be a positive integer (above 0)");
        }
        $this->productId = $productId;
    }
    
    
    /**
     * Checks for equality of listing item ids.
     *
     * @param ListingItemId $other
     *
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->productId === $other->productId;
    }
    
    
    /**
     * @return int
     */
    public function asInt(): int
    {
        return $this->productId;
    }
}