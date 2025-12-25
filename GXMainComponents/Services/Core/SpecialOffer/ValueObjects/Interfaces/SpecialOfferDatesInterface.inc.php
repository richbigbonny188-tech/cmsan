<?php
/* --------------------------------------------------------------
   SpecialOfferDatesInterface.inc.php 2018-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferDatesInterface
 */
interface SpecialOfferDatesInterface
{
    /**
     * Returns the start date of the special offer.
     *
     * @return \DateTime Start date of special offer.
     */
    public function begins();
    
    
    /**
     * Returns the expiration date of the special offer.
     *
     * @return \DateTime Expiration date of special offer.
     */
    public function expires();
    
    
    /**
     * Returns the creation date of the special offer.
     *
     * @return \DateTime|null Creation date of special offer.
     */
    public function added();
    
    
    /**
     * Returns the last modification date of the special offer.
     *
     * @return \DateTime|null Last modification date of special offer.
     */
    public function modified();
}
