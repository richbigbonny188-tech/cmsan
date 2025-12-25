<?php
/* --------------------------------------------------------------
   GxAdapter.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Adapters;

use CI_DB_query_builder;
use DataCache;
use GXModulesCache;
use HubPublic\Http\CurlRequest;
use InvalidArgumentException;
use LanguageCode;
use LogControl;
use MainFactory;
use StaticGXCoreLoader;
use StringType;
use ThemeControl;

/**
 * Class GxAdapter
 *
 * @package Gambio\AdminFeed\Adapters
 */
class GxAdapter
{
    /**
     * @return mixed
     */
    public function mainFactoryCreate()
    {
        $args      = func_get_args();
        $className = $args[0];
        
        array_shift($args);
        
        $classObject = MainFactory::create_object($className, $args);
        
        if ($classObject === false) {
            throw new InvalidArgumentException('Class not found in registry: ' . $className);
        }
        
        return $classObject;
    }
    
    
    /**
     * @return mixed
     */
    public function mainFactoryCreateObject($p_class_name, $p_args_array = [], $p_use_singleton = false)
    {
        return MainFactory::create_object($p_class_name, $p_args_array, $p_use_singleton);
    }
    
    
    /**
     * @return array
     */
    public function getGxModulesFiles()
    {
        return GXModulesCache::getFiles();
    }
    
    
    /**
     * @return CI_DB_query_builder
     */
    public function getQueryBuilder()
    {
        return StaticGXCoreLoader::getDatabaseQueryBuilder();
    }
    
    
    /**
     * @return ThemeControl
     */
    public function getThemeControl()
    {
        return StaticGXCoreLoader::getThemeControl();
    }
    
    
    /**
     * @return mixed
     */
    public function getSessionValue($key)
    {
        return $_SESSION[$key];
    }
    
    
    /**
     * @return DataCache
     */
    public function getDataCache()
    {
        return DataCache::get_instance();
    }
    
    
    /**
     * @return LogControl
     */
    public function getLogControl()
    {
        return LogControl::get_instance();
    }
    
    
    /**
     * @return LanguageCode
     */
    public function getCurrentLanguageCode()
    {
        return new LanguageCode(new StringType($_SESSION['language_code']));
    }
    
    
    /**
     * @return CurlRequest
     */
    public function getHubCurlRequest()
    {
        return new CurlRequest();
    }
}