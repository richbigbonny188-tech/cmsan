<?php
/* --------------------------------------------------------------
   CronjobServiceFactory.inc.php 2018-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

/**
 * Class CronjobServiceFactory
 */
class CronjobServiceFactory
{
    /**
     * @var \CronjobService
     */
    protected static $service;
    
    /**
     * @var \CronjobTaskService
     */
    protected static $taskService;
    
    /**
     * @var \CronjobRepository
     */
    protected static $repository;
    
    
    /**
     * Creates and in memory caches cronjob service.
     *
     * @return \CronjobService
     */
    public static function createService()
    {
        if (null === static::$service) {
            static::$service = MainFactory::create(CronjobService::class, static::_createRepository());
        }
        
        return static::$service;
    }
    
    
    /**
     * Returns an instance of cronjob repository.
     *
     * @return \CronjobRepository
     */
    protected static function _createRepository()
    {
        if (null === static::$repository) {
            $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $legacyContainer = LegacyDependencyContainer::getInstance();
            $storageBuilder = $legacyContainer->get(ConfigurationStorageRepositoryBuilder::class);
            
            $settings = MainFactory::create(CronjobSettings::class, new \ExistingDirectory(DIR_FS_CATALOG));
            $reader   = MainFactory::create(CronjobReader::class, $db, $settings, $storageBuilder);
            $writer   = MainFactory::create(CronjobWriter::class, $db, $storageBuilder);
            
            static::$repository = MainFactory::create(CronjobRepository::class, $reader, $settings, $writer);
        }
        
        return static::$repository;
    }
    
    
    /**
     * Creates and in memory caches cronjob task service.
     *
     * @return \CronjobTaskService
     */
    public static function createTaskService()
    {
        if (null === static::$taskService) {
            $settings            = MainFactory::create(CronjobSettings::class, new \ExistingDirectory(DIR_FS_CATALOG));
            $storage             = new CronjobConfigurationStorage(StaticGXCoreLoader::getDatabaseQueryBuilder(),
                                                                   $settings);
            $repository          = MainFactory::create(CronjobTaskRepository::class, $settings, $storage);
            static::$taskService = MainFactory::create(CronjobTaskService::class, $repository);
        }
        
        return static::$taskService;
    }
    
    
    /**
     * Creates a cronjob configuration storage.
     *
     * @return CronjobConfigurationStorage
     */
    public static function createCronjobConfigurationStorage()
    {
        $settings = MainFactory::create(CronjobSettings::class, new \ExistingDirectory(DIR_FS_CATALOG));
        
        return MainFactory::create(CronjobConfigurationStorage::class,
                                   StaticGXCoreLoader::getDatabaseQueryBuilder(),
                                   $settings);
    }
}
