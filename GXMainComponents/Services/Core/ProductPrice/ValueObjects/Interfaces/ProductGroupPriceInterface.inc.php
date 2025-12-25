<?php
/* --------------------------------------------------------------
   ProductGroupPriceInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductGroupPriceInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage ValueObjects
 */
interface ProductGroupPriceInterface
{
    /**
     * Returns the customer group id.
     *
     * @return int Customer group id.
     */
    public function customerGroupId();
    
    
    /**
     * Returns the group price.
     *
     * @return double|null Group price.
     */
    public function groupPrice();
    
    
    /**
     * Returns the graduated prices.
     *
     * @return GraduatedPriceCollection|null
     */
    public function graduatedPrices();
}