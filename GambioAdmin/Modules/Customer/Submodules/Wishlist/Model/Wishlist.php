<?php
/*--------------------------------------------------------------
   Wishlist.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model;

use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\Collections\WishlistItems;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\CustomerId;

class Wishlist
{
    /**
     * @var CustomerId
     */
    private CustomerId    $customerId;
    
    /**
     * @var WishlistItems
     */
    private WishlistItems $items;
    
    
    /**
     * Constructor
     */
    private function __construct(CustomerId $customerId, WishlistItems $items)
    {
        $this->customerId = $customerId;
        $this->items      = $items;
    }
    
    
    /**
     * Creates a new instance of Wishlist.
     */
    public static function create(CustomerId $customerId, WishlistItems $items): self
    {
        return new self($customerId, $items);
    }
    
    
    /**
     * Returns the Customer ID.
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * Returns the Wishlist Items.
     */
    public function items(): WishlistItems
    {
        return $this->items;
    }
    
    
    /**
     * Returns the contents of this Value Object as an array.
     */
    public function toArray(string $format = "Y-m-d"): array
    {
        return [
            "customerId" => $this->customerId->value(),
            "items"      => $this->items->toArray($format)
        ];
    }
}