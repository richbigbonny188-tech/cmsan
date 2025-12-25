<?php
/* --------------------------------------------------------------
   SpecialOfferInterface.inc.php 2018-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferInterface
 */
interface SpecialOfferInterface
{
    /**
     * Returns the special offer id.
     *
     * @return int|null Id of special offer.
     */
    public function id();
    
    
    /**
     * Returns the product id of the special.
     *
     * @return int Id of special offer's product.
     */
    public function productId();
    
    
    /**
     * Returns the special offer's price.
     *
     * @return double Price of special offer.
     */
    public function price();
    
    
    /**
     * Returns the quantity of the special offer.
     *
     * @return double Special offer's quantity.
     */
    public function quantity();
    
    
    /**
     * Returns the special offer status.
     *
     * @return bool True if special offer is active.
     */
    public function status();
    
    
    /**
     * Returns the creation date of the special offer.
     *
     * @return \DateTime Creation date of special offer.
     */
    public function added();
    
    
    /**
     * Returns the last modification date of the special offer.
     *
     * @return \DateTime Last modification date of special offer.
     */
    public function modified();
    
    
    /**
     * Returns the expiration date of the special offer.
     *
     * @return \DateTime Expiration date of special offer.
     */
    public function expires();
    
    
    /**
     * Returns the start date of the special offer.
     *
     * @return \DateTime Start date of special offer.
     */
    public function begins();
}
