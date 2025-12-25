<?php

/* --------------------------------------------------------------
   CustomerGroupSettingsInterface.inc.php 2017-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupSettingsInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage ValueObjects
 */
interface CustomerGroupSettingsInterface
{
    /**
     * Checks if the customer group is public or not.
     *
     * @return bool
     */
    public function isPublic();
    
    
    /**
     * Checks if ot discount flag enable or not.
     *
     * @return bool
     */
    public function isOtDiscountFlag();
    
    
    /**
     * Checks if graduated prices enable or not.
     *
     * @return bool
     */
    public function isGraduatedPrices();
    
    
    /**
     * Checks if show price enable or not.
     *
     * @return bool
     */
    public function isShowPrice();
    
    
    /**
     * Checks if show price tax enable or not.
     *
     * @return bool
     */
    public function isShowPriceTax();
    
    
    /**
     * Checks if add tax ot enable or not.
     *
     * @return bool
     */
    public function isAddTaxOt();
    
    
    /**
     * Checks if discount attributes enable or not.
     *
     * @return bool
     */
    public function isDiscountAttributes();
    
    
    /**
     * Checks if fsk 18 purchasable or not.
     *
     * @return bool
     */
    public function isFsk18Purchasable();
    
    
    /**
     * Checks if fsk 18 display enable or not.
     *
     * @return bool
     */
    public function isFsk18Display();
    
    
    /**
     * Checks if write reviews enable or not.
     *
     * @return bool
     */
    public function isWriteReviews();
    
    
    /**
     * Checks if read reviews enable or not.
     *
     * @return bool
     */
    public function isReadReviews();
}