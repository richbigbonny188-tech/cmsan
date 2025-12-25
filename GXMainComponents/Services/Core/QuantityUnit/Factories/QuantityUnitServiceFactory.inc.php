<?php

/* --------------------------------------------------------------
 QuantityUnitServiceFactory.inc.php 2017-08-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class QuantityUnitServiceFactory
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Factories
 */
class QuantityUnitServiceFactory
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \QuantityUnitReadService
     */
    protected $readService;
    
    /**
     * @var \QuantityUnitWriteService
     */
    protected $writeService;
    
    /**
     * @var \QuantityUnitRepository
     */
    protected $repository;
    
    /**
     * @var \QuantityUnitStorage
     */
    protected $storage;
    
    /**
     * @var \QuantityUnitFactory
     */
    protected $factory;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * QuantityUnitServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates a new instance of QuantityUnitReadService or returns the existing one.
     *
     * @return \QuantityUnitReadService
     */
    public function createReadService()
    {
        if (null === $this->readService) {
            $this->readService = MainFactory::create('QuantityUnitReadService', $this->_createRepository());
        }
        
        return $this->readService;
    }
    
    
    /**
     * Creates a new instance of QuantityUnitWriteService or returns the existing one.
     *
     * @return \QuantityUnitWriteService
     */
    public function createWriteService()
    {
        if (null === $this->writeService) {
            $this->writeService = MainFactory::create('QuantityUnitWriteService', $this->_createRepository());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * Creates a new instance of QuantityUnitRepository or returns the existing one.
     *
     * @return \QuantityUnitRepository
     */
    protected function _createRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create('QuantityUnitRepository',
                                                    $this->_createStorage(),
                                                    $this->_createFactory(),
                                                    $this->_createLanguageProvider());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Creates a new instance of QuantityUnitStorage or returns the existing one.
     *
     * @return \QuantityUnitStorage
     */
    protected function _createStorage()
    {
        if (null === $this->storage) {
            $this->storage = MainFactory::create('QuantityUnitStorage',
                                                 $this->queryBuilder,
                                                 $this->_createLanguageProvider());
        }
        
        return $this->storage;
    }
    
    
    /**
     * Creates a new instance of QuantityUnitFactory or returns the existing one.
     *
     * @return \QuantityUnitFactory
     */
    protected function _createFactory()
    {
        if (null === $this->factory) {
            $this->factory = MainFactory::create('QuantityUnitFactory');
        }
        
        return $this->factory;
    }
    
    
    /**
     * Creates a new instance of LanguageProvider or returns the existing one.
     *
     * @return \LanguageProvider
     */
    protected function _createLanguageProvider()
    {
        if (null === $this->languageProvider) {
            $this->languageProvider = MainFactory::create('LanguageProvider', $this->queryBuilder);
        }
        
        return $this->languageProvider;
    }
}