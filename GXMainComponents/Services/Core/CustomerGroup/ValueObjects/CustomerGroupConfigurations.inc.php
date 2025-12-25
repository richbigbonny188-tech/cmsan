<?php

/* --------------------------------------------------------------
  CustomerGroupConfigurations.inc.php 2017-09-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class GXEngineQuantityUnit.
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage ValueObjects
 */
class CustomerGroupConfigurations implements CustomerGroupConfigurationsInterface
{
    /**
     * @var double
     */
    protected $minOrder;
    
    /**
     * @var double
     */
    protected $maxOrder;
    
    /**
     * @var double
     */
    protected $discount;
    
    /**
     * @var double
     */
    protected $otDiscount;
    
    /**
     * @var array
     */
    protected $unallowedPaymentModules;
    
    /**
     * @var array
     */
    protected $unallowedShippingModules;
    
    
    /**
     * CustomerGroupConfigurations constructor.
     *
     * @param DecimalType $discount
     * @param DecimalType $otDiscount
     * @param DecimalType $minOrder
     * @param DecimalType $maxOrder
     * @param array       $unallowedPaymentModules
     * @param array       $unallowedShippingModules
     */
    public function __construct(
        DecimalType $discount,
        DecimalType $otDiscount,
        DecimalType $minOrder = null,
        DecimalType $maxOrder = null,
        array $unallowedPaymentModules = [],
        array $unallowedShippingModules = []
    ) {
        $this->discount                 = $discount->asDecimal();
        $this->otDiscount               = $otDiscount->asDecimal();
        $this->minOrder                 = $minOrder ? $minOrder->asDecimal() : null;
        $this->maxOrder                 = $maxOrder ? $maxOrder->asDecimal() : null;
        $this->unallowedPaymentModules  = $unallowedPaymentModules;
        $this->unallowedShippingModules = $unallowedShippingModules;
    }
    
    
    /**
     * Returns the minimum order.
     *
     * @return double The minimum order.
     */
    public function getMinOrder()
    {
        return $this->minOrder;
    }
    
    
    /**
     * Returns the maximum order.
     *
     * @return double The maximum order.
     */
    public function getMaxOrder()
    {
        return $this->maxOrder;
    }
    
    
    /**
     * Returns the discount.
     *
     * @return double The discount.
     */
    public function getDiscount()
    {
        return $this->discount;
    }
    
    
    /**
     * Returns the ot discount.
     *
     * @return double The ot discount.
     */
    public function getOtDiscount()
    {
        return $this->otDiscount;
    }
    
    
    /**
     * Returns the unallowed payment modules.
     *
     * @return array The unallowed payment modules.
     */
    public function getUnallowedPaymentModules()
    {
        return $this->unallowedPaymentModules;
    }
    
    
    /**
     * Returns the unallowed shipping modules.
     *
     * @return array The unallowed shipping modules.
     */
    public function getUnallowedShippingModules()
    {
        return $this->unallowedShippingModules;
    }
}