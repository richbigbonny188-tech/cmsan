<?php
/* --------------------------------------------------------------
   SharedShoppingCartReader.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartReader
 *
 * @category   System
 * @package    SharedShoppingCart
 */
class SharedShoppingCartReader implements SharedShoppingCartReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SharedShoppingCartReader constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Gets the content in JSON format of the shopping cart corresponding to the hash
     *
     * @param StringType $shoppingCartHash Hash of the shopping cart
     *
     * @return string JSON representation of the shopping cart
     */
    public function getShoppingCart(StringType $shoppingCartHash)
    {
        return $this->db->get_where('shared_shopping_carts', ['hash' => $shoppingCartHash->asString()])
                   ->row_array()['json_shopping_cart'];
    }
    
    
    /**
     * Gets a collection of all shared shopping carts by a given customer ID.
     *
     * @param \IdType $customerId
     *
     * @return \SharedShoppingCartCollection
     */
    public function getShoppingCartsByCustomerId(IdType $customerId)
    {
        $shoppingCarts    = [];
        $rawShoppingCarts = $this->db->get_where('shared_shopping_carts', ['customer_id' => $customerId->asInt()])
            ->result_array();
        
        foreach ($rawShoppingCarts as $shoppingCartArray) {
            $shoppingCarts[] = $this->createSharedShoppingCartFromArray($shoppingCartArray);
        }
        
        return new SharedShoppingCartCollection($shoppingCarts);
    }
    
    
    /**
     * Creates a shared shopping cart instance from an array.
     *
     * @param array $rawShoppingCart
     *
     * @return bool|\SharedShoppingCart
     *
     * @throws InvalidArgumentException
     */
    protected function createSharedShoppingCartFromArray(array $rawShoppingCart)
    {
        return MainFactory::create(SharedShoppingCart::class,
                                   new StringType($rawShoppingCart['hash']),
                                   new StringType($rawShoppingCart['json_shopping_cart']),
                                   new DateTime($rawShoppingCart['creation_date']),
                                   new IdType($rawShoppingCart['customer_id']));
    }
}