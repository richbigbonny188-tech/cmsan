<?php
/* --------------------------------------------------------------
   ProductPriceWriterInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPriceWriterInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Repositories
 */
interface ProductPriceWriterInterface
{
    /**
     * Updates the given product price data to a storage.
     *
     * @param array   $productPrice Product price data to be updated.
     * @param \IdType $productId    Id of product price to be updated.
     *
     * @return void
     */
    public function update(array $productPrice, IdType $productId);
    
    
    /**
     * Deletes a product price from the storage by the given id.
     *
     * @param \IdType $productId Id of product price to be removed.
     *
     * @return void
     */
    public function delete(IdType $productId);
}