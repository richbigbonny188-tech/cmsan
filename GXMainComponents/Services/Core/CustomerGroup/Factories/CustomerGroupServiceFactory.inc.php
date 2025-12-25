<?php

/* --------------------------------------------------------------
  CustomerGroupServiceFactory.inc.php 2017-09-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class CustomerGroupServiceFactory
 * *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Factories
 */
class CustomerGroupServiceFactory
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \CustomerGroupAccessRepository
     */
    protected $accessRepository;
    
    /**
     * @var \CustomerGroupWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var \CustomerGroupReadServiceInterface
     */
    protected $readerService;
    
    /**
     * @var \CustomerGroupFactory
     */
    protected $factory;
    
    
    /**
     * CustomerGroupServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder for database connection.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates a customer group write service.
     *
     * @return  \CustomerGroupWriteServiceInterface
     */
    public function createWriteService()
    {
        $this->_createFactory()->_createWriter();
        
        return $this->writeService;
    }
    
    
    /**
     * Creates a customer group read service.
     *
     * @return  \CustomerGroupReadServiceInterface
     */
    public function createReadService()
    {
        $this->_createRepository()->_createReader();
        
        return $this->readerService;
    }
    
    
    public function createFactory()
    {
        $this->_createFactory();
        
        return $this->factory;
    }
    
    
    /**
     * Creates a new instance of a CustomerGroupRepository object.
     * Consecutive calls provide the same object.
     *
     * @return \CustomerGroupServiceFactory
     */
    protected function _createRepository()
    {
        if (null === $this->accessRepository) {
            $languageProvider = MainFactory::create('LanguageProvider', $this->queryBuilder);
            $writer           = MainFactory::create('CustomerGroupWriter', $this->queryBuilder, $languageProvider);
            $deleter          = MainFactory::create('CustomerGroupDeleter', $this->queryBuilder);
            $repository       = MainFactory::create('CustomerGroupRepository', $writer, $deleter);
            $reader           = MainFactory::create('CustomerGroupReader', $this->queryBuilder);
            $factory          = MainFactory::create('CustomerGroupFactory', $repository);
            
            $this->accessRepository = MainFactory::create('CustomerGroupAccessRepository',
                                                          $factory,
                                                          $reader,
                                                          $languageProvider);
        }
        
        return $this;
    }
    
    
    /**
     * Creates a new instance of a CustomerGroupReadService object.
     * Consecutive calls provide the same object.
     *
     * @return \CustomerGroupServiceFactory
     */
    protected function _createReader()
    {
        if (null === $this->readerService) {
            $this->readerService = MainFactory::create('CustomerGroupReadService', $this->accessRepository);
        }
        
        return $this;
    }
    
    
    /**
     * Creates a new instance of a CustomerGroupFactory object.
     * Consecutive call provide the same Object.
     *
     * @return $this
     */
    protected function _createFactory()
    {
        if (null === $this->factory) {
            $languageProvider = MainFactory::create('LanguageProvider', $this->queryBuilder);
            $writer           = MainFactory::create('CustomerGroupWriter', $this->queryBuilder, $languageProvider);
            $deleter          = MainFactory::create('CustomerGroupDeleter', $this->queryBuilder);
            $repository       = MainFactory::create('CustomerGroupRepository', $writer, $deleter);
            $this->factory    = MainFactory::create('CustomerGroupFactory', $repository);
        }
        
        return $this;
    }
    
    
    /**
     * Creates a new instance of a CustomerGroupWritService object.
     * Consecutive calls provide the same object.
     *
     * @return \CustomerGroupServiceFactory
     */
    protected function _createWriter()
    {
        if (null === $this->writeService) {
            $this->writeService = MainFactory::create('CustomerGroupWriteService', $this->factory);
        }
        
        return $this;
    }
}