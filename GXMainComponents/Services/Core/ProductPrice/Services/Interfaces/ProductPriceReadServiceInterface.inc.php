<?php
/* --------------------------------------------------------------
   ProductPriceReadServiceInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPriceReadServiceInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Services
 */
interface ProductPriceReadServiceInterface
{
    /**
     * Returns product price by given id.
     *
     * @param \IdType $productId Product id.
     *
     * @return \ProductPriceInterface
     */
    public function getById(IdType $productId);
    
    
    /**
     * Returns the product quantity price.
     *
     * @param \IdType      $productId       Product id.
     * @param \IdType      $customerGroupId Customer group id.
     * @param \DecimalType $quantity        Quantity.
     *
     * @return \GraduatedPriceInterface
     */
    public function getByQuantity(IdType $productId, IdType $customerGroupId, DecimalType $quantity);
}