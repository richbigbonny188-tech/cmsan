<?php
/* --------------------------------------------------------------
   SpecialOfferInformationInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferInformationInterface
 */
interface SpecialOfferInformationInterface
{
    /**
     * Returns the quantity of the special offer.
     *
     * @return double Special offer's quantity.
     */
    public function quantity();
    
    
    /**
     * Returns the special offer's price.
     *
     * @return double Price of special offer.
     */
    public function price();
    
    
    /**
     * Returns the special offer status.
     *
     * @return bool True if special offer is active.
     */
    public function status();
    
    
    /**
     * Returns the productId the special offer belongs to.
     *
     * @return int ID of the offered product.
     */
    public function productId();
}
