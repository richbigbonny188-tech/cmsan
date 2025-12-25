<?php
/* --------------------------------------------------------------
   ModulesDetailsMapper.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Mapper;

use Gambio\AdminFeed\Services\ShopInformation\Collections\ModuleDetailsCollection;
use Gambio\AdminFeed\Services\ShopInformation\Reader\ModulesDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ModuleDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ModulesDetails;

/**
 * Class ModulesDetailsMapper
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Mapper
 */
class ModulesDetailsMapper
{
    /**
     * @var ModulesDetailsReader
     */
    private $reader;
    
    
    /**
     * ModulesDetailsMapper constructor.
     *
     * @param ModulesDetailsReader $reader
     */
    public function __construct(ModulesDetailsReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * returns the modules details.
     *
     * @return ModulesDetails
     */
    public function getModulesDetails()
    {
        $hubModules          = $this->createCollection($this->reader->getHubModulesData());
        $paymentModules      = $this->createCollection($this->reader->getPaymentModulesData());
        $shippingModules     = $this->createCollection($this->reader->getShippingModulesData());
        $orderTotalModules   = $this->createCollection($this->reader->getOrderTotalModulesData());
        $moduleCenterModules = $this->createCollection($this->reader->getModuleCenterModulesData());
        
        return new ModulesDetails($hubModules,
                                  $paymentModules,
                                  $shippingModules,
                                  $orderTotalModules,
                                  $moduleCenterModules);
    }
    
    
    /**
     * @param array $modulesData
     *
     * @return ModuleDetailsCollection
     */
    private function createCollection($modulesData)
    {
        $collection = new ModuleDetailsCollection();
        foreach ($modulesData as $moduleName => $moduleData) {
            $collection->add(new ModuleDetails($moduleName, $moduleData['installed'], $moduleData['enabled']));
        }
        
        return $collection;
    }
}