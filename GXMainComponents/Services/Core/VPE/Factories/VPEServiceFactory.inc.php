<?php

/* --------------------------------------------------------------
 VPEServiceFactory.inc.php 2017-07-31
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class VPEServiceFactory
 *
 * @category   System
 * @package    VPE
 * @subpackage Factories
 */
class VPEServiceFactory
{
    /**
     * @var \VPERepository
     */
    protected $repository;
    
    /**
     * @var \VPEStorage
     */
    protected $storage;
    
    /**
     * @var \VPEFactory
     */
    protected $factory;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * VPEServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates an instance of a vpe read service.
     *
     * @return \VPEReadService
     */
    public function createVpeReadService()
    {
        return MainFactory::create('VPEReadService', $this->_createVpeRepository());
    }
    
    
    /**
     * Creates an instance of a vpe write service.
     *
     * @return array|bool|\VPEWriteService
     */
    public function createVpeWriteService()
    {
        return MainFactory::create('VPEWriteService', $this->_createVpeRepository());
    }
    
    
    /**
     * Creates an instance of VPERepository.
     *
     * @return \VPERepository
     */
    protected function _createVpeRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create('VPERepository',
                                                    $this->_createVpeStorage(),
                                                    $this->_createVpeFactory(),
                                                    $this->_createLanguageProvider());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Creates an instance of VPEStorage.
     *
     * @return \VPEStorage
     */
    protected function _createVpeStorage()
    {
        if (null === $this->storage) {
            $this->storage = MainFactory::create('VPEStorage', $this->queryBuilder, $this->_createLanguageProvider());
        }
        
        return $this->storage;
    }
    
    
    /**
     * Creates an instance of VPEFactory.
     *
     * @return \VPEFactory
     */
    protected function _createVpeFactory()
    {
        if (null === $this->factory) {
            $this->factory = MainFactory::create('VPEFactory');
        }
        
        return $this->factory;
    }
    
    
    /**
     * Creates an instance of LanguageProvider.
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