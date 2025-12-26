<?php
/* --------------------------------------------------------------
   ProductPriceWriterRepository.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPriceWriterRepositoryInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Services
 */
interface ProductPriceWriteServiceInterface
{
    /**
     * Saves product price entity in the storage.
     *
     * @param \ProductPriceInterface $productPrice Product price to be save.
     * @param \IdType                $productId    Id of product to be updated.
     *
     * @return ProductPriceInterface
     * @throws \EntityNotFoundException
     */
    public function save(ProductPriceInterface $productPrice, IdType $productId);
    
    
    /**
     * Deletes the product price from the storage.
     *
     * @param \ProductPriceInterface $productPrice Product price to be removed.
     *
     * @return void
     */
    public function delete(ProductPriceInterface $productPrice);
}