<?php
/* --------------------------------------------------------------
   FeaturedProductSettingsInterface.inc.php 2019-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface FeaturedProductSettingsInterface
 */
interface FeaturedProductSettingsInterface
{
    /**
     * Return the limit of products to displayed.
     *
     * @return int
     */
    public function getLimit();
    
    
    /**
     *checks if random order selected.
     *
     * @return bool
     */
    public function getRandomOrder();
    
    
    /**
     * Return the customer group id.
     *
     * @return int
     */
    public function getCustomerGroupId();
    
    
    /**
     * Checks if fsk 18 enable or not.
     *
     * @return bool
     */
    public function isFsk18Purchasable();
    
    
    /**
     * Return the language code.
     *
     * @return string
     */
    public function languageCode();
}