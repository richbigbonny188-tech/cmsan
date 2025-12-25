<?php
/* --------------------------------------------------------------
   ProductPriceReaderInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPriceReaderInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Repositories
 */
interface ProductPriceReaderInterface
{
    /**
     * Fetches product price data by the given id.
     *
     * @param \IdType $productId product id.
     *
     * @return array Raw data of product prices.
     */
    public function fetchById(IdType $productId);
    
    
    /**
     * Fetches product price data by the given conditions.
     *
     * @param \IdType      $productId       Product id.
     * @param \IdType      $customerGroupId Customer group id.
     * @param \DecimalType $quantity        Quantity.
     *
     * @return array Raw data of product prices.
     */
    public function fetchByQuantity(IdType $productId, IdType $customerGroupId, DecimalType $quantity);
}