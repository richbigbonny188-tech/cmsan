<?php
/*--------------------------------------------------------------
   ShoppingCart.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\Collections\ShoppingCartItems;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;

class ShoppingCart
{
    /**
     * @var CustomerId
     */
    private CustomerId $customerId;
    
    /**
     * @var ShoppingCartItems
     */
    private ShoppingCartItems $items;
    
    
    /**
     * Constructor
     */
    private function __construct(CustomerId $customerId, ShoppingCartItems $items)
    {
        $this->customerId = $customerId;
        $this->items      = $items;
    }
    
    
    /**
     * Creates a new instance of Shopping Cart
     */
    public static function create(CustomerId $customerId, ShoppingCartItems $items): self
    {
        return new self($customerId, $items);
    }
    
    
    /**
     * Returns the Customer ID
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * Returns the Shopping Cart Items
     */
    public function items(): ShoppingCartItems
    {
        return $this->items;
    }
    
    
    /**
     * Returns an array containing the class members
     */
    public function toArray($format = "Y-m-d"): array
    {
        return [
            "customerId" => $this->customerId->value(),
            "items"      => $this->items->toArray($format)
        ];
    }
}