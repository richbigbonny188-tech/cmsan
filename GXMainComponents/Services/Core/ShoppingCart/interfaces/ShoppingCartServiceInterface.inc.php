<?php
/* --------------------------------------------------------------
   ShoppingCartServiceInterface.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ShoppingCartServiceInterface
 *
 * @category   System
 * @package    ShoppingCart
 * @subpackage Interfaces
 */
interface ShoppingCartServiceInterface
{
    /**
     * Deletes all shared shopping carts by a given customer ID.
     *
     * @param \IdType $customerId
     */
    public function deleteShoppingCartsByCustomerId(IdType $customerId);
    
    
    /**
     * Gets a collection of all shopping carts by a given customer ID.
     *
     * @param \IdType $customerId
     *
     * @return \ShoppingCartCollection
     */
    public function getShoppingCartsByCustomerId(IdType $customerId);
}