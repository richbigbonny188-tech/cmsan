<?php
/* --------------------------------------------------------------
   CartContent.php 2016-11-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Collections\CartItemCollection;
use HubPublic\Collections\CartTotalItemCollection;

/**
 * Class CartContent
 *
 * @package HubPublic\ValueObjects
 */
class CartContent
{
    /**
     * Collection of cart items
     *
     * @var \HubPublic\Collections\CartItemCollection
     */
    private $cartItems;
    
    /**
     * Collection of auxiliary cart items
     *
     * @var CartTotalItemCollection
     */
    private $cartTotalItems;
    
    /**
     * Total price
     *
     * @var float
     */
    private $totalPrice;
    
    /**
     * Shipping cost
     *
     * @var float
     */
    private $shippingCost;
    
    
    /**
     * CartContent constructor.
     *
     * @param \HubPublic\Collections\CartItemCollection $cartItems  Collection of cart items
     * @param float                                     $totalPrice Total price of all items in cart
     * @param float                                     $shippingCost
     * @param CartTotalItemCollection                   $cartTotalItems
     */
    public function __construct(
        CartItemCollection $cartItems,
        float $totalPrice,
        float $shippingCost,
        CartTotalItemCollection $cartTotalItems
    ) {
        $this->cartItems      = $cartItems;
        $this->totalPrice     = $totalPrice;
        $this->shippingCost   = $shippingCost;
        $this->cartTotalItems = $cartTotalItems;
    }
    
    
    /**
     * Returns a collection of all items in cart.
     *
     * @return \HubPublic\Collections\CartItemCollection Cart item collection
     */
    public function getCartItemCollection(): CartItemCollection
    {
        return $this->cartItems;
    }
    
    
    /**
     * Returns the total price of all items in cart.
     *
     * @return float Total price
     */
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }
    
    
    /**
     * @return CartTotalItemCollection
     */
    public function getCartTotalItemCollection(): CartTotalItemCollection
    {
        return $this->cartTotalItems;
    }
    
    
    /**
     * @return float
     */
    public function getShippingCost(): float
    {
        return $this->shippingCost;
    }
}
