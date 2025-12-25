<?php
/* --------------------------------------------------------------
   ManufacturerServiceFactory.inc.php 2017-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ManufacturerServiceFactory
{
    /**
     * @var ManufacturerReadService
     */
    protected $readService;
    
    /**
     * @var ManufacturerWriteService
     */
    protected $writeService;
    
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var ManufacturerRepository
     */
    protected $repository;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * ManufacturerServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates and returns a manufacturer read service.
     *
     * @return \ManufacturerReadService
     */
    public function createReadService()
    {
        if (null === $this->readService) {
            $this->readService = MainFactory::create('ManufacturerReadService', $this->_createRepository());
        }
        
        return $this->readService;
    }
    
    
    /**
     * Creates and returns a manufacturer write service.
     *
     * @return \ManufacturerWriteService
     */
    public function createWriteService()
    {
        if (null === $this->writeService) {
            $this->writeService = MainFactory::create('ManufacturerWriteService',
                                                      $this->_createRepository(),
                                                      MainFactory::create('ImageFileStorage',
                                                                          MainFactory::create('WritableDirectory',
                                                                                              DIR_FS_CATALOG
                                                                                              . 'images/manufacturers')));
        }
        
        return $this->writeService;
    }
    
    
    /**
     * Creates and returns a manufacturer repository.
     *
     * @return \ManufacturerRepository
     */
    protected function _createRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create('ManufacturerRepository',
                                                    MainFactory::create('ManufacturerFactory'),
                                                    MainFactory::create('ManufacturerReader', $this->queryBuilder),
                                                    MainFactory::create('ManufacturerWriter',
                                                                        $this->queryBuilder,
                                                                        $this->_createLanguageProvider()),
                                                    MainFactory::create('ManufacturerDeleter', $this->queryBuilder),
                                                    $this->_createLanguageProvider());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Creates and returns a language provider instance.
     *
     * @return bool|\LanguageProvider
     */
    protected function _createLanguageProvider()
    {
        if (null === $this->languageProvider) {
            $this->languageProvider = MainFactory::create('LanguageProvider', $this->queryBuilder);
        }
        
        return $this->languageProvider;
    }
}