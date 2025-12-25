<?php
/* --------------------------------------------------------------
   ModulesDetailsReader.php 2019-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Reader;

use CacheControl;
use CI_DB_query_builder;
use Gambio\AdminFeed\Adapters\GxAdapter;
use Gambio\AdminFeed\Adapters\GxAdapterTrait;
use Gambio\AdminFeed\Services\ShopInformation\HubClient;
use Gambio\AdminFeed\Services\ShopInformation\Settings;
use LanguageTextManager;
use ModuleCenterModuleInterface;

/**
 * Class ModulesDetailsReader
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Reader
 */
class ModulesDetailsReader
{
    use GxAdapterTrait;
    
    /**
     * @var Settings
     */
    private $settings;
    
    /**
     * @var CI_DB_query_builder
     */
    private $db;
    
    /**
     * @var HubClient
     */
    private $hubClient;
    
    
    /**
     * ModulesDetailsReader constructor.
     *
     * @param Settings            $settings
     * @param CI_DB_query_builder $db
     * @param HubClient           $hubClient
     */
    public function __construct(Settings $settings, CI_DB_query_builder $db, HubClient $hubClient)
    {
        $this->settings  = $settings;
        $this->db        = $db;
        $this->hubClient = $hubClient;
    }
    
    
    /**
     * Returns the hub modules data.
     *
     * @return array
     */
    public function getHubModulesData()
    {
        $modules     = [];
        $modulesData = $this->hubClient->getHubModulesData();
        
        if (!is_array($modulesData) || count($modulesData) === 0) {
            return [];
        }
        
        foreach ($modulesData as $moduleData) {
            $modules[$moduleData['code']] = [
                'installed' => $moduleData['isInstalled'],
                'enabled'   => $moduleData['isActive'],
            ];
        }
        
        return $modules;
    }
    
    
    /**
     * Returns the payment modules data.
     *
     * @return array
     */
    public function getPaymentModulesData()
    {
        $modules     = [];
        $moduleFiles = (array)glob($this->settings->getBaseDirectory() . 'includes/modules/payment/*.php');
        
        foreach ($moduleFiles as $moduleFile) {
            $moduleClass  = basename($moduleFile, '.php');
            $moduleStatus = $this->getModuleStatus($moduleClass, 'payment');
            
            $modules[$moduleClass] = [
                'installed' => $moduleStatus !== null,
                'enabled'   => $moduleStatus === 'True',
            ];
        }
        
        return $modules;
    }
    
    
    /**
     * Returns the shipping modules data.
     *
     * @return array
     */
    public function getShippingModulesData()
    {
        $modules     = [];
        $moduleFiles = (array)glob($this->settings->getBaseDirectory() . 'includes/modules/shipping/*.php');
        
        foreach ($moduleFiles as $moduleFile) {
            $moduleClass  = basename($moduleFile, '.php');
            $moduleStatus = $this->getModuleStatus($moduleClass, 'shipping');
            
            $modules[$moduleClass] = [
                'installed' => $moduleStatus !== null,
                'enabled'   => $moduleStatus === 'True',
            ];
        }
        
        return $modules;
    }
    
    
    /**
     * Returns the order total modules data.
     *
     * @return array
     */
    public function getOrderTotalModulesData()
    {
        $modules     = [];
        $moduleFiles = (array)glob($this->settings->getBaseDirectory() . 'includes/modules/order_total/*.php');
        
        foreach ($moduleFiles as $moduleFile) {
            $moduleClass  = basename($moduleFile, '.php');
            $moduleCode   = (substr($moduleClass, 0, 3) !== 'ot_') ? $moduleClass : substr($moduleClass, 3);
            $moduleStatus = $this->getModuleStatus($moduleCode, 'order_total');
            
            $modules[$moduleClass] = [
                'installed' => $moduleStatus !== null,
                'enabled'   => $moduleStatus === 'True',
            ];
        }
        
        return $modules;
    }
    
    
    /**
     * Returns the module center modules data.
     *
     * @return array
     */
    public function getModuleCenterModulesData()
    {
        $modules          = [];
        $moduleCollection = $this->collectModuleCenterModules();
        
        /** @var ModuleCenterModuleInterface $module */
        foreach ($moduleCollection as $module) {
            $modules[$module->getName()] = [
                'installed' => $module->isInstalled(),
                'enabled'   => null,
            ];
        }
        
        return $modules;
    }
    
    
    /**
     * @param string $module
     * @param string $type
     *
     * @return bool|null
     */
    private function getModuleStatus($module, $type)
    {
        $configurationKey = 'MODULE_' . strtoupper($type) . '_' . strtoupper($module) . '_STATUS';
        
        $status = $this->db->select('value')
            ->from('gx_configurations')
            ->where('key',
                    "configuration/$configurationKey")
            ->get()
            ->row_array();
        
        if (!isset($status) || !is_array($status) || empty($status['value'])) {
            return null;
        }
        
        return $status['value'];
    }
    
    
    /**
     * @return array
     */
    private function collectModuleCenterModules()
    {
        $adapter = $this->gxAdapter();
        
        $db                  = $adapter->getQueryBuilder();
        $languageTextManager = $adapter->mainFactoryCreate('LanguageTextManager', 'module_center_module');
        $cacheControl        = $adapter->mainFactoryCreateObject('CacheControl');
        
        $collection = array_merge($this->collectMainModuleCenterModules($adapter,
                                                                        $db,
                                                                        $languageTextManager,
                                                                        $cacheControl),
                                  $this->collectGxModuleCenterModules($adapter,
                                                                      $db,
                                                                      $languageTextManager,
                                                                      $cacheControl),
                                  $this->collectGxModulesJsonModuleCenterModules($adapter,
                                                                                 $db,
                                                                                 $languageTextManager,
                                                                                 $cacheControl));
        
        return $collection;
    }
    
    
    /**
     * @param GxAdapter           $adapter
     * @param CI_DB_query_builder $db
     * @param LanguageTextManager $languageTextManager
     * @param CacheControl        $cacheControl
     *
     * @return array
     */
    private function collectMainModuleCenterModules(
        $adapter,
        $db,
        $languageTextManager,
        $cacheControl
    ) {
        $modules = [];
        
        $moduleFiles = (array)glob($this->settings->getBaseDirectory()
                                   . 'GXMainComponents/Modules/*ModuleCenterModule.inc.php');
        foreach ($moduleFiles as $file) {
            $moduleName = strtok(basename($file), '.');
            $modules[]  = $adapter->mainFactoryCreate($moduleName, $languageTextManager, $db, $cacheControl);
        }
        
        return $modules;
    }
    
    
    /**
     * @param GxAdapter           $adapter
     * @param CI_DB_query_builder $db
     * @param LanguageTextManager $languageTextManager
     * @param CacheControl        $cacheControl
     *
     * @return array
     */
    private function collectGxModuleCenterModules(
        $adapter,
        $db,
        $languageTextManager,
        $cacheControl
    ) {
        $modules = [];
        
        $gxModuleFiles = $adapter->getGxModulesFiles();
        foreach ($gxModuleFiles as $file) {
            if (strpos($file, 'ModuleCenterModule.inc.php') !== false) {
                $moduleName = strtok(basename($file), '.');
                $modules[]  = $adapter->mainFactoryCreate($moduleName, $languageTextManager, $db, $cacheControl);
            }
        }
        
        return $modules;
    }
    
    
    /**
     * @param GxAdapter           $adapter
     * @param CI_DB_query_builder $db
     * @param LanguageTextManager $languageTextManager
     * @param CacheControl        $cacheControl
     *
     * @return array
     */
    private function collectGxModulesJsonModuleCenterModules(
        $adapter,
        $db,
        $languageTextManager,
        $cacheControl
    ) {
        $adapter = $this->gxAdapter();
        $modules = [];
        
        $gxModuleFiles = $adapter->getGxModulesFiles();
        foreach ($gxModuleFiles as $file) {
            if (stripos($file, 'GXModule.json') !== false) {
                preg_match('/GXModules\/(.*)\/GXModule.json/', $file, $matches);
                $gxModuleName = $matches[1];
                
                $module = $adapter->mainFactoryCreate('GXModuleCenterModule', $languageTextManager, $db, $cacheControl);
                $module->setName($gxModuleName);
                
                $modules[] = $module;
            }
        }
        
        return $modules;
    }
}