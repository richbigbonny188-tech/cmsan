<?php
/*--------------------------------------------------------------
   ShoppingCartItemDeleted.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\ShoppingCartItemId;

class ShoppingCartItemDeleted
{
    /**
     * @var CustomerId
     */
    private CustomerId $customerId;
    
    /**
     * @var ShoppingCartItemId
     */
    private ShoppingCartItemId $shoppingCartItemId;
    
    
    /**
     * Constructor
     */
    private function __construct(CustomerId $customerId, ShoppingCartItemId $shoppingCartItemId)
    {
        $this->customerId         = $customerId;
        $this->shoppingCartItemId = $shoppingCartItemId;
    }
    
    
    /**
     * Creates a new instance of ShoppingCartItemDeleted
     */
    public static function create(CustomerId $customerId, ShoppingCartItemId $shoppingCartItemId): self
    {
        return new self($customerId, $shoppingCartItemId);
    }
    
    
    /**
     * Return the Shopping Cart Item ID
     */
    public function shoppingCartItemId(): ShoppingCartItemId
    {
        return $this->shoppingCartItemId;
    }
    
    
    /**
     * Returns the Customer ID
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
}