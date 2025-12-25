<?php

/* --------------------------------------------------------------
   OrderStatusServiceFactory.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatusServiceFactory
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Factories
 */
class OrderStatusServiceFactory implements OrderStatusServiceFactoryInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var OrderStatusRepository
     */
    protected $repository;
    
    /**
     * @var OrderStatusFactory
     */
    protected $factory;
    
    /**
     * @var OrderStatusReader
     */
    protected $reader;
    
    /**
     * @var OrderStatusWriter
     */
    protected $writer;
    
    /**
     * @var OrderStatusDeleter
     */
    protected $deleter;
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * OrderStatusServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder Active record instance for data access.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates a new instance of OrderStatusService.
     *
     * @return OrderStatusService
     */
    public function createService()
    {
        return MainFactory::create('OrderStatusService', $this->_getRepository());
    }
    
    
    /**
     * Returns the order status repository.
     *
     * @return \OrderStatusRepository
     */
    protected function _getRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create('OrderStatusRepository',
                                                    $this->_getFactory(),
                                                    $this->_getReader(),
                                                    $this->_getWriter(),
                                                    $this->_getDeleter(),
                                                    $this->_getLanguageProvider());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Returns the order status factory.
     *
     * @return \OrderStatusFactory
     */
    protected function _getFactory()
    {
        if (null === $this->factory) {
            $this->factory = MainFactory::create('OrderStatusFactory');
        }
        
        return $this->factory;
    }
    
    
    /**
     * Returns the order status reader.
     *
     * @return \OrderStatusReader
     */
    protected function _getReader()
    {
        if (null === $this->reader) {
            $this->reader = MainFactory::create('OrderStatusReader', $this->queryBuilder);
        }
        
        return $this->reader;
    }
    
    
    /**
     * Returns the order status writer.
     *
     * @return \OrderStatusWriter
     */
    protected function _getWriter()
    {
        if (null === $this->writer) {
            $this->writer = MainFactory::create('OrderStatusWriter',
                                                $this->queryBuilder,
                                                $this->_getLanguageProvider());
        }
        
        return $this->writer;
    }
    
    
    /**
     * Returns the order status deleter.
     *
     * @return \OrderStatusDeleter
     */
    protected function _getDeleter()
    {
        if (null === $this->deleter) {
            $this->deleter = MainFactory::create('OrderStatusDeleter', $this->queryBuilder);
        }
        
        return $this->deleter;
    }
    
    
    /**
     * Returns the language provider.
     *
     * @return \LanguageProvider
     */
    protected function _getLanguageProvider()
    {
        if (null === $this->languageProvider) {
            $this->languageProvider = MainFactory::create('LanguageProvider', $this->queryBuilder);
        }
        
        return $this->languageProvider;
    }
}