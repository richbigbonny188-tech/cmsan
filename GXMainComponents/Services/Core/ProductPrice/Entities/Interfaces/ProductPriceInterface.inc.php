<?php
/* --------------------------------------------------------------
   ProductPriceInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPriceInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage ValueObjects
 */
interface ProductPriceInterface
{
    /**
     * Returns the product id.
     *
     * @return int Product id.
     */
    public function productId();
    
    
    /**
     * Returns the product price
     *
     * @return double Product price.
     */
    public function price();
    
    
    /**
     * Returns the tax class id.
     *
     * @return int Tax class id.
     */
    public function taxClassId();
    
    
    /**
     * Returns the group prices.
     *
     * @return ProductGroupPriceCollection|null
     */
    public function groupPrices();
}