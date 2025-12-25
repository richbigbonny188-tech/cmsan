<?php
/* --------------------------------------------------------------
   ModulesDetails.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\ValueObjects;

use Gambio\AdminFeed\Services\ShopInformation\Collections\ModuleDetailsCollection;

/**
 * Class ModulesDetails
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\ValueObjects
 */
class ModulesDetails
{
    /**
     * @var ModuleDetailsCollection
     */
    private $payment;
    
    /**
     * @var ModuleDetailsCollection
     */
    private $shipping;
    
    /**
     * @var ModuleDetailsCollection
     */
    private $orderTotal;
    
    /**
     * @var ModuleDetailsCollection
     */
    private $hub;
    
    /**
     * @var ModuleDetailsCollection
     */
    private $moduleCenter;
    
    
    /**
     * ModulesDetails constructor.
     *
     * @param ModuleDetailsCollection $hub
     * @param ModuleDetailsCollection $payment
     * @param ModuleDetailsCollection $shipping
     * @param ModuleDetailsCollection $orderTotal
     * @param ModuleDetailsCollection $moduleCenter
     */
    public function __construct(
        ModuleDetailsCollection $hub,
        ModuleDetailsCollection $payment,
        ModuleDetailsCollection $shipping,
        ModuleDetailsCollection $orderTotal,
        ModuleDetailsCollection $moduleCenter
    ) {
        $this->hub          = $hub;
        $this->payment      = $payment;
        $this->shipping     = $shipping;
        $this->orderTotal   = $orderTotal;
        $this->moduleCenter = $moduleCenter;
    }
    
    
    /**
     * Creates and returns a new ModulesDetails instance.
     *
     * @param ModuleDetailsCollection $hub
     * @param ModuleDetailsCollection $payment
     * @param ModuleDetailsCollection $shipping
     * @param ModuleDetailsCollection $orderTotal
     * @param ModuleDetailsCollection $moduleCenter
     *
     * @return ModulesDetails
     */
    static function create(
        ModuleDetailsCollection $hub,
        ModuleDetailsCollection $payment,
        ModuleDetailsCollection $shipping,
        ModuleDetailsCollection $orderTotal,
        ModuleDetailsCollection $moduleCenter
    ) {
        return new self($hub, $payment, $shipping, $orderTotal, $moduleCenter);
    }
    
    
    /**
     * Returns the hub modules.
     *
     * @return ModuleDetailsCollection
     */
    public function hub()
    {
        return $this->hub;
    }
    
    
    /**
     * Returns the payment modules.
     *
     * @return ModuleDetailsCollection
     */
    public function payment()
    {
        return $this->payment;
    }
    
    
    /**
     * Returns the shipping modules.
     *
     * @return ModuleDetailsCollection
     */
    public function shipping()
    {
        return $this->shipping;
    }
    
    
    /**
     * Returns the order total modules.
     *
     * @return ModuleDetailsCollection
     */
    public function orderTotal()
    {
        return $this->orderTotal;
    }
    
    
    /**
     * Returns the module center modules.
     *
     * @return ModuleDetailsCollection
     */
    public function moduleCenter()
    {
        return $this->moduleCenter;
    }
}