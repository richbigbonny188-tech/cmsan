<?php
/* --------------------------------------------------------------
  GXModuleCenterModule.inc.php 2020-10-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

use Gambio\Admin\Application\GambioAdminBootstrapper;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\DependencyInjection\Abstraction\LeagueContainer;

/**
 * Class GXModuleCenterModule
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class GXModuleCenterModule extends AbstractModuleCenterModule
{
    protected $moduleData;
    
    
    protected function _init()
    {
    }
    
    
    public function setModuleData($data)
    {
        $this->moduleData = $data;
    }
    
    
    /**
     * Set the tile of the module to show in the oveview
     *
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    
    /**
     * Set the name of the module to use in ajax requests
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    
    /**
     * Set the sort order of module in overview
     *
     * @param $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * Set the version of the module
     *
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
    
    
    /**
     * Set the description of the module to display in overview
     *
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    
    /**
     * Installs a GXModule and sets active
     *
     */
    public function install()
    {
        parent::install();
        $namespace            = $this->getName();
        $configurationStorage = MainFactory::create('GXModuleConfigurationStorage', $namespace);
        $configurationStorage->set('active', true);
        
        $cacheControl = MainFactory::create_object('CacheControl');
        $cacheControl->clear_data_cache();
        @unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
        
        $installData = $this->_getInstallMethod($namespace);
        if ($installData) {
            $this->_callUserMethod($installData);
        }
    }
    
    
    /**
     * Uninstalls a GXModule an deletes everything in GXModuleConfigurationStorage
     *
     */
    public function uninstall()
    {
        parent::uninstall();
        $namespace            = $this->getName();
        $configurationStorage = MainFactory::create('GXModuleConfigurationStorage', $namespace);
        $configurationStorage->delete_all();
        
        $uninstallData = $this->_getUninstallMethod($namespace);
        if ($uninstallData) {
            $this->_callUserMethod($uninstallData);
        }
        
        $cacheControl = MainFactory::create_object('CacheControl');
        $cacheControl->clear_data_cache();
        @unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
    }
    
    
    /**
     * Loads the json data of the GXModule
     *
     * @param $name
     *
     * @return array|bool|mixed
     */
    protected function _getGXModule($name)
    {
        $gxModuleFiles = GXModulesCache::getFiles();
        
        foreach ($gxModuleFiles as $file) {
            if (strpos($file, 'GXModule.json') !== false) {
                preg_match("/GXModules\/(.*)\/GXModule.json/", $file, $matches);
                $moduleData = json_decode(file_get_contents($file), true);
                if (str_replace('/', '', $matches[1]) === $name) {
                    return $moduleData;
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * Get the controller and method for install
     *
     * @param $module
     *
     * @return bool|mixed
     */
    protected function _getInstallMethod($module)
    {
        $data = $this->_getGXModule($module);
        
        $this->setModuleData($data);
        
        if (isset($data['install'])) {
            return $data['install'];
        } else {
            return false;
        }
    }
    
    
    /**
     * Get the controller and method for uninstall
     *
     * @param $module
     *
     * @return bool|mixed
     */
    protected function _getUninstallMethod($module)
    {
        $data = $this->_getGXModule($module);
        
        $this->setModuleData($data);
        
        if (isset($data['uninstall'])) {
            return $data['uninstall'];
        } else {
            return false;
        }
    }
    
    
    /**
     * Call the method from the controller
     *
     * @param $action
     */
    protected function _callUserMethod($action)
    {
        $method         = $action['method'];
        $controllerName = $action['controller'];
        
        $application       = new Application(LeagueContainer::create());
        $adminBootstrapper = new GambioAdminBootstrapper();
        $adminBootstrapper->boot($application);
        if ($application->has($controllerName)) {
            $controller = $application->get($controllerName);
            $controller->{$method}($this->moduleData);
            
            return;
        }
        
        $controller = MainFactory::create($controllerName);
        $controller->{$method}($this->db, $this->moduleData, $this->languageTextManager, $this->cacheControl);
    }
}