<?php
/* --------------------------------------------------------------
   GraduatedPriceInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface GraduatedPriceInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage ValueObjects
 */
interface GraduatedPriceInterface
{
    /**
     * Returns the threshold of the graduated price.
     *
     * @return double Graduated price´s quantity.
     */
    public function threshold();
    
    
    /**
     * Returns the graduated price.
     *
     * @return double Graduated price.
     */
    public function graduatedPrice();
    
    
    /**
     * Returns the tax class id.
     *
     * @return int Tax class id.
     */
    public function taxClassId();
}