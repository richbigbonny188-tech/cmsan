<?php
/* --------------------------------------------------------------
   SpecialOffersServiceFactory.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOffersServiceFactory
 */
class SpecialOffersServiceFactory
{
    /**
     * @var SpecialOfferReadService
     */
    protected static $readService;
    
    /**
     * @var SpecialOfferWriteService
     */
    protected static $writeService;
    
    /**
     * @var SpecialOfferRepository
     */
    protected static $repository;
    
    
    /**
     * Returns an instance of special offer read service.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \SpecialOfferReadService Special offer read service.
     */
    public static function readService()
    {
        if (null === static::$readService) {
            static::$readService = MainFactory::create(SpecialOfferReadService::class, static::_createRepository());
        }
        
        return static::$readService;
    }
    
    
    /**
     * Returns an instance of special offer write service.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \SpecialOfferWriteService Special offer write service.
     */
    public static function writeService()
    {
        if (null === static::$writeService) {
            static::$writeService = MainFactory::create(SpecialOfferWriteService::class, static::_createRepository());
        }
        
        return static::$writeService;
    }
    
    
    /**
     * Returns an instance of special offer repository.
     * The returned instance will be in memory cached and returned on consecutive calls.
     *
     * @return \SpecialOfferRepository Special offer repository.
     */
    protected static function _createRepository()
    {
        if (null === static::$repository) {
            $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
            
            $reader      = MainFactory::create(SpecialOfferReader::class, $db);
            $writer      = MainFactory::create(SpecialOfferWriter::class, $db);
            $dataAdapter = MainFactory::create(SpecialOfferDataAdapter::class, $reader, $writer);
            $mapper      = MainFactory::create(SpecialOfferMapper::class, $dataAdapter);
            
            static::$repository = MainFactory::create(SpecialOfferRepository::class, $mapper);
        }
        
        return static::$repository;
    }
}