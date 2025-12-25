<?php
/*--------------------------------------------------------------
   CustomerCartMapper.php 2022-11-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ShoppingCart;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\ShoppingCartItem;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartFactory;

class CustomerCartMapper
{
    /**
     * @var ShoppingCartFactory
     */
    private ShoppingCartFactory $factory;
    
    
    /**
     * Constructor
     */
    public function __construct(ShoppingCartFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * Maps the Data of a single Shopping Cart Item into a Shopping Cart Item instance.
     */
    public function mapShoppingCartItem(array $cartData): ShoppingCartItem
    {
        $selectedOptions = [];
        
        foreach ($cartData['selected_options'] as $option) {
            $selectedOptions[] = $this->factory->createSelectedOption($option['option_name'],
                                                                      $option['option_value']);
        }
        
        $itemNumber = [];
        
        if ($cartData['products_model'] !== '') {
            $itemNumber[] = $cartData['products_model'];
        }
        
        if ($cartData['combi_model'] !== '') {
            $itemNumber[] = $cartData['combi_model'];
        }
        
        return $this->factory->createShoppingCartItem($cartData['products_id'],
                                                      (int)$cartData['customers_basket_id'],
                                                      implode('-', $itemNumber),
                                                      (string)$cartData['products_name'],
                                                      (string)$cartData['image'],
                                                      (int)$cartData['amount'],
                                                      new DateTimeImmutable($cartData['added_at'] ?? ''),
                                                      $this->factory->createSelectedOptions(...$selectedOptions));
    }
    
    
    /**
     * Maps the Date of a Shopping Cart with a given customer into a Shopping Cart instance
     */
    public function mapShoppingCart(array $cartsData, CustomerId $customerId): ShoppingCart
    {
        $cartItems = [];
        
        foreach ($cartsData as $cart) {
            $cartItems[] = $this->mapShoppingCartItem($cart);
        }
        
        return ShoppingCart::create($customerId, $this->factory->createShoppingCartItems(...$cartItems));
    }
}