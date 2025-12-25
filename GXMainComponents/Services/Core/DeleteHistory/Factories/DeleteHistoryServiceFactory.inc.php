<?php
/* --------------------------------------------------------------
   DeleteHistoryServiceFactory.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryServiceFactory
 */
class DeleteHistoryServiceFactory
{
    /**
     * @var \DeleteHistoryReadService
     */
    protected static $readService;
    
    /**
     * @var \DeleteHistoryWriteService
     */
    protected static $writeService;
    
    /**
     * @var \DeleteHistoryRepository
     */
    protected static $repository;
    
    
    /**
     * Returns an instance of delete history read service.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \DeleteHistoryReadService Delete history read service.
     */
    public static function readService()
    {
        if (null === static::$readService) {
            static::$readService = MainFactory::create(DeleteHistoryReadService::class, static::_createRepository());
        }
        
        return static::$readService;
    }
    
    
    /**
     * Returns an instance of delete history write service.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \DeleteHistoryWriteService Delete history write service.
     */
    public static function writeService()
    {
        if (null === static::$writeService) {
            static::$writeService = MainFactory::create(DeleteHistoryWriteService::class, static::_createRepository());
        }
        
        return static::$writeService;
    }
    
    
    /**
     * Returns an instance of delete history repository.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \DeleteHistoryRepository Delete history repository.
     */
    protected static function _createRepository()
    {
        if (null === static::$repository) {
            $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
            
            $reader             = MainFactory::create(DeleteHistoryReader::class, $db);
            $writer             = MainFactory::create(DeleteHistoryWriter::class, $db);
            $dataAdapter        = MainFactory::create(DeleteHistoryDataAdapter::class, $reader, $writer);
            $mapper             = MainFactory::create(DeleteHistoryMapper::class, $dataAdapter);
            static::$repository = MainFactory::create(DeleteHistoryRepository::class, $mapper);
        }
        
        return static::$repository;
    }
}