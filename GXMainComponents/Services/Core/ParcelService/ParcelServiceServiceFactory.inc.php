<?php
/* --------------------------------------------------------------
   ParcelServiceServiceFactory.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceServiceFactory
 */
class ParcelServiceServiceFactory
{
    /**
     * @var \ParcelServiceReadService
     */
    protected static $readService;
    
    /**
     * @var \ParcelServiceWriteService
     */
    protected static $writeService;
    
    /**
     * @var \ParcelServiceRepository
     */
    protected static $repository;
    
    
    /**
     * Returns an instance of parcel service read service.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \ParcelServiceReadService Parcel service read service.
     */
    public static function readService()
    {
        if (null === static::$readService) {
            static::$readService = MainFactory::create(ParcelServiceReadService::class, static::_createRepository());
        }
        
        return static::$readService;
    }
    
    
    /**
     * Returns an instance of parcel service write service.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \ParcelServiceWriteService Parcel service write service.
     */
    public static function writeService()
    {
        if (null === static::$writeService) {
            static::$writeService = MainFactory::create(ParcelServiceWriteService::class, static::_createRepository());
        }
        
        return static::$writeService;
    }
    
    
    /**
     * Returns an instance of parcel service repository.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \ParcelServiceRepository Parcel service repository.
     */
    protected static function _createRepository()
    {
        if (null === static::$repository) {
            $db                 = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $reader             = MainFactory::create(GXParcelServiceReader::class, $db);
            $writer             = MainFactory::create(GXParcelServiceWriter::class, $db);
            $dataAdapter        = MainFactory::create(ParcelServiceDataAdapter::class, $reader, $writer);
            $mapper             = MainFactory::create(ParcelServiceMapper::class, $dataAdapter);
            static::$repository = MainFactory::create(ParcelServiceRepository::class, $mapper);
        }
        
        return static::$repository;
    }
}