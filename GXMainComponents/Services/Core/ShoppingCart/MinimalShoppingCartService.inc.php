<?php
/* --------------------------------------------------------------
   MinimalShoppingCartService.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class MinimalShoppingCartService
 */
class MinimalShoppingCartService implements ShoppingCartServiceInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * MinimalShoppingCartService constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Deletes all shopping carts from DB by a given customer ID.
     *
     * @param \IdType $customerId
     */
    public function deleteShoppingCartsByCustomerId(IdType $customerId)
    {
        $this->db->delete('customers_basket', ['customers_id' => $customerId->asInt()]);
        $this->db->delete('customers_basket_attributes', ['customers_id' => $customerId->asInt()]);
    }
    
    
    /**
     * Gets a collection of all shopping carts by a given customer ID.
     *
     * @param \IdType $customerId
     *
     * @return \ShoppingCartCollection
     */
    public function getShoppingCartsByCustomerId(IdType $customerId)
    {
        $shoppingCarts    = [];
        $rawShoppingCarts = $this->db->get_where('customers_basket', ['customers_id' => $customerId->asInt()])
            ->result_array();
        
        foreach ($rawShoppingCarts as $shoppingCartArray) {
            $shoppingCarts[] = $this->createSharedShoppingCartFromArray($shoppingCartArray);
        }
        
        return new ShoppingCartCollection($shoppingCarts);
    }
    
    
    /**
     * Creates a shopping cart instance from an array.
     *
     * @param array $rawShoppingCart
     *
     * @return bool|\ShoppingCart
     *
     * @throws InvalidArgumentException
     */
    protected function createSharedShoppingCartFromArray(array $rawShoppingCart)
    {
        return MainFactory::create(GXEngineShoppingCart::class,
                                   new IdType($rawShoppingCart['customers_basket_id']),
                                   new IdType($rawShoppingCart['customers_id']),
                                   new StringType($rawShoppingCart['products_id']),
                                   new DecimalType($rawShoppingCart['customers_basket_quantity']),
                                   new DecimalType($rawShoppingCart['final_price']),
                                   new StringType($rawShoppingCart['final_price']));
    }
}