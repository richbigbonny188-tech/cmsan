<?php
/* --------------------------------------------------------------
   ProductPriceRepositoryInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPriceRepositoryInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Repositories
 */
interface ProductPriceRepositoryInterface
{
    /**
     * Returns the product price by given product id.
     *
     * @param \IdType $productId Product id.
     *
     * @return \ProductPriceInterface|null
     */
    public function findById(IdType $productId);
    
    
    /**
     * Returns the product price that matches the given search condition.
     *
     * @param \IdType      $productId       Product id.
     * @param \IdType      $customerGroupId Customer group id.
     * @param \DecimalType $quantity        Quantity.
     *
     * @return \GraduatedPriceInterface|null
     */
    public function findByQuantity(IdType $productId, IdType $customerGroupId, DecimalType $quantity);
    
    
    /**
     * Saves or updates the given product price in the storage.
     *
     * @param \ProductPriceInterface $productPrice Product price to be saved or updated.
     * @param \IdType                $productId    Id of product to be updated.
     *
     * @return ProductPriceInterface
     * @throws \EntityNotFoundException
     */
    public function save(ProductPriceInterface $productPrice, IdType $productId);
    
    
    /**
     * Deletes the given product price from the storage.
     *
     * @param \ProductPriceInterface $productPrice Product price to be deleted.
     *
     * @return ProductPriceInterface
     */
    public function delete(ProductPriceInterface $productPrice);
}