<?php
/* --------------------------------------------------------------
   ProductPriceServiceFactory.inc.php 2018-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceServiceFactory
 */
class ProductPriceServiceFactory
{
    /**
     * @var \ProductPriceReadService
     */
    protected static $readService;
    
    /**
     * @var \ProductPriceWriteService
     */
    protected static $writeService;
    
    /**
     * @var \ProductPriceRepository
     */
    protected static $repository;
    
    /**
     * @var \ProductPriceSerializer
     */
    protected static $serializer;
    
    /**
     * @var \ProductGroupPriceSerializer
     */
    protected static $groupPriceSerializer;
    
    /**
     * @var \GraduatedPriceSerializer
     */
    protected static $quantityPriceSerializer;
    
    
    /**
     * Returns an instance of product price read service.
     * The returned instance will be in memory cached an returned on consecutive calls.
     *
     * @return \ProductPriceReadService Product price read service.
     */
    public static function readService()
    {
        if (null === static::$readService) {
            static::$readService = MainFactory::create(ProductPriceReadService::class, static::_createRepository());
        }
        
        return static::$readService;
    }
    
    
    /**
     * Returns an instance of product price write service.
     * The returned instance will be in memory cached an returned on consecutive calls.
     *
     * @return \ProductPriceWriteService Product price write service.
     */
    public static function writeService()
    {
        if (null === static::$writeService) {
            static::$writeService = MainFactory::create(ProductPriceWriteService::class, static::_createRepository());
        }
        
        return static::$writeService;
    }
    
    
    /**
     * Returns an instance of product price serializer.
     * The returned instance will be in memory cached an returned on consecutive calls.
     *
     * @return \ProductPriceSerializer
     */
    public static function serializer()
    {
        if (null === static::$serializer) {
            $groupPriceSerializer = static::_createGroupPriceSerializer();
            static::$serializer   = MainFactory::create(ProductPriceSerializer::class, $groupPriceSerializer);
        }
        
        return static::$serializer;
    }
    
    
    /**
     * Returns an instance of product quantity price serializer.
     * The returned instance will be in memory cached an returned on consecutive calls.
     *
     * @return \GraduatedPriceSerializer
     */
    public static function createQuantityPriceSerializer()
    {
        if (null === static::$quantityPriceSerializer) {
            static::$quantityPriceSerializer = MainFactory::create(GraduatedPriceSerializer::class);
        }
        
        return static::$quantityPriceSerializer;
    }
    
    
    /**
     * Returns an instance of product price repository.
     * The returned instance will be in memory cached an returned on consecutive calls.
     *
     * @return \ProductPriceRepository Product price repository.
     */
    protected static function _createRepository()
    {
        if (null === static::$repository) {
            $db                 = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $reader             = MainFactory::create(ProductPriceReader::class, $db);
            $writer             = MainFactory::create(ProductPriceWriter::class, $db);
            $dataAdapter        = MainFactory::create(ProductPriceAdapter::class, $reader, $writer);
            $mapper             = MainFactory::create(ProductPriceMapper::class, $dataAdapter);
            static::$repository = MainFactory::create(ProductPriceRepository::class, $mapper);
        }
        
        return static::$repository;
    }
    
    
    /**
     * Returns an instance of product group price serializer.
     * The returned instance will be in memory cached an returned on consecutive calls.
     *
     * @return \ProductGroupPriceSerializer
     */
    protected static function _createGroupPriceSerializer()
    {
        if (null === static::$groupPriceSerializer) {
            $quantityPriceSerializer      = static::createQuantityPriceSerializer();
            static::$groupPriceSerializer = MainFactory::create(ProductGroupPriceSerializer::class,
                                                                $quantityPriceSerializer);
        }
        
        return static::$groupPriceSerializer;
    }
}