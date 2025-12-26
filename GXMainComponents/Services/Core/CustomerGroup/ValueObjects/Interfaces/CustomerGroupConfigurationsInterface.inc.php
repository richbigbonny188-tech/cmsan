<?php
/* --------------------------------------------------------------
   CustomerGroupConfigurationInterface.inc.php 2017-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupConfigurationsInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage ValueObjects
 */
interface CustomerGroupConfigurationsInterface
{
    /**
     * Returns the minimum order.
     *
     * @return double The minimum order.
     */
    public function getMinOrder();
    
    
    /**
     * Returns the maximum order.
     *
     * @return double The maximum order.
     */
    public function getMaxOrder();
    
    
    /**
     * Returns the discount.
     *
     * @return double The discount.
     */
    public function getDiscount();
    
    
    /**
     * Returns the ot discount.
     *
     * @return double The ot discount.
     */
    public function getOtDiscount();
    
    
    /**
     * Returns the unallowed payment modules.
     *
     * @return array The unallowed payment modules.
     */
    public function getUnallowedPaymentModules();
    
    
    /**
     * Returns the unallowed shipping modules.
     *
     * @return array The unallowed shippings.
     */
    public function getUnallowedShippingModules();
}