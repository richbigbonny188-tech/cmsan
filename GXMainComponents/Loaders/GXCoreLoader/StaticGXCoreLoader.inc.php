<?php
/* --------------------------------------------------------------
   StaticGXCoreLoader.inc.php 2020-06-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticGXCoreLoader
 *
 * This class is for static use only.
 * Usage example: $customerService = StaticGXCoreLoader::getService('Customer');
 *
 * @category    System
 * @package     Loaders
 * @subpackage  GXCoreLoader
 */
class StaticGXCoreLoader
{
    /**
     * GXCoreLoader Instance
     *
     * @var GXCoreLoaderInterface
     */
    protected static $gxCoreLoader;
    
    
    /**
     * Class Constructor
     *
     * @throws BadFunctionCallException
     */
    public function __construct()
    {
        throw new BadFunctionCallException('This class is for static use only.');
    }
    
    
    /**
     * Get a service object instance.
     *
     * @param string $serviceName
     *
     * @return object
     */
    public static function getService($serviceName)
    {
        $gxCoreLoader = self::_getGXCoreLoader();
        $service      = $gxCoreLoader->getService($serviceName);
        
        return $service;
    }
    
    
    /**
     * Method depends on CodeIgniter database library.
     *
     * @return CI_DB_query_builder
     */
    public static function getDatabaseQueryBuilder()
    {
        $gxCoreLoader         = self::_getGXCoreLoader();
        $databaseQueryBuilder = $gxCoreLoader->getDatabaseQueryBuilder();
        
        return $databaseQueryBuilder;
    }
    
    
    /**
     * Method depends on PHP DebugBar library.
     *
     * @return DebugBar
     */
    public static function getDebugBar()
    {
        $gxCoreLoader     = self::_getGXCoreLoader();
        $standardDebugBar = $gxCoreLoader->getDebugBar();
        
        return $standardDebugBar;
    }
    
    
    /**
     * Method depends on PHP DebugBar library.
     *
     * @return array
     */
    public static function getDebugBarAssets()
    {
        $gxCoreLoader   = self::_getGXCoreLoader();
        $debugBarAssets = $gxCoreLoader->getDebugBarAssets();
        
        return $debugBarAssets;
    }
    
    
    /**
     * Method depends on CodeIgniter database library.
     *
     * @return CI_DB_utility
     */
    public static function getDatabaseUtilityHelper()
    {
        $gxCoreLoader          = self::_getGXCoreLoader();
        $databaseUtilityHelper = $gxCoreLoader->getDatabaseUtilityHelper();
        
        return $databaseUtilityHelper;
    }
    
    
    /**
     * Method depends on CodeIgniter database library.
     *
     * @return CI_DB_forge
     */
    public static function getDatabaseForgeHelper()
    {
        $gxCoreLoader        = self::_getGXCoreLoader();
        $databaseForgeHelper = $gxCoreLoader->getDatabaseForgeHelper();
        
        return $databaseForgeHelper;
    }
    
    
    /**
     * Returns an instance of the ThemeControl.
     *
     * @return \ThemeControl
     */
    public static function getThemeControl()
    {
        $gxCoreLoader = self::_getGXCoreLoader();
        $themeControl = $gxCoreLoader->getThemeControl();
        
        return $themeControl;
    }
    
    
    /**
     * Get GX Core Loader object instance.
     *
     * @return GXCoreLoaderInterface
     */
    protected static function _getGXCoreLoader()
    {
        if (self::$gxCoreLoader === null) {
            /**
             * If the MainFactory cache is not fully built, the creation of the GXCoreLoaderSettings and GXCoreLoader
             * objects fails, because of interdependencies. Including the necessary files beforehand solves the problem.
             */
            include_once DIR_FS_CATALOG . 'GXMainComponents/Loaders/GXCoreLoader/Interfaces/GXCoreLoaderSettingsInterface.inc.php';
            include_once DIR_FS_CATALOG . 'GXMainComponents/Loaders/GXCoreLoader/GXCoreLoaderSettings.inc.php';
            include_once DIR_FS_CATALOG . 'GXMainComponents/Loaders/GXCoreLoader/Interfaces/GXCoreLoaderInterface.inc.php';
            include_once DIR_FS_CATALOG . 'GXMainComponents/Loaders/GXCoreLoader/GXCoreLoader.inc.php';
            
            $gxCoreLoaderSettings = MainFactory::create('GXCoreLoaderSettings');
            self::$gxCoreLoader   = MainFactory::create('GXCoreLoader', $gxCoreLoaderSettings);
        }
        
        return self::$gxCoreLoader;
    }
}
