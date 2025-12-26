<?php
/* --------------------------------------------------------------
   AgreementServiceFactory.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementServiceFactory
 *
 * @category   System
 * @package    Agreement
 * @subpackage Factories
 */
class AgreementServiceFactory
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \AgreementFactory
     */
    protected $factory;
    
    /**
     * @var \AgreementRepository
     */
    protected $repository;
    
    /**
     * @var \AgreementAccessRepository
     */
    protected $accessRepository;
    
    /**
     * @var \AgreementReader
     */
    protected $reader;
    
    /**
     * @var \AgreementWriter
     */
    protected $writer;
    
    /**
     * @var \AgreementDeleter
     */
    protected $deleter;
    
    /**
     * @var \AgreementReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var \AgreementWriteServiceInterface
     */
    protected $writeService;
    
    
    /**
     * AgreementServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates an agreement write service.
     *
     * @return \AgreementWriteServiceInterface
     */
    public function createWriteService()
    {
        if (null === $this->writeService) {
            $this->writeService = MainFactory::create(AgreementWriteService::class, $this->_createFactory());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * Creates an agreement write service.
     *
     * @return \AgreementReadServiceInterface
     */
    public function createReadService()
    {
        if (null === $this->readService) {
            $this->readService = MainFactory::create(AgreementReadService::class, $this->_createAccessRepository());
        }
        
        return $this->readService;
    }
    
    
    /**
     * Creates a new instance of an AgreementWriter.
     * Consecutive calls provide the same object.
     *
     * @return \AgreementWriter
     */
    protected function _createWriter()
    {
        if (null === $this->writer) {
            $this->writer = MainFactory::create(AgreementWriter::class, $this->queryBuilder);
        }
        
        return $this->writer;
    }
    
    
    /**
     * Creates a new instance of an AgreementDeleter.
     * Consecutive calls provide the same object.
     *
     * @return \AgreementDeleter
     */
    protected function _createDeleter()
    {
        if (null === $this->deleter) {
            $this->deleter = MainFactory::create(AgreementDeleter::class, $this->queryBuilder);
        }
        
        return $this->deleter;
    }
    
    
    /**
     * Creates a new instance of AgreementReader.
     * Consecutive calls provide the same object.
     *
     * @return \AgreementReader
     */
    protected function _createReader()
    {
        if (null === $this->reader) {
            $this->reader = MainFactory::create('AgreementReader', $this->queryBuilder);
        }
        
        return $this->reader;
    }
    
    
    /**
     * Creates a new instance of an AgreementRepository.
     * Consecutive calls provide the same object.
     *
     * @return \AgreementRepository
     */
    protected function _createRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create(AgreementRepository::class,
                                                    $this->_createWriter(),
                                                    $this->_createDeleter());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Creates a new instance of AgreementFactory.
     * Consecutive calls provide the same object.
     *
     * @return \AgreementFactory
     */
    protected function _createFactory()
    {
        if (null === $this->factory) {
            $this->factory = MainFactory::create(AgreementFactory::class, $this->_createRepository());
        }
        
        return $this->factory;
    }
    
    
    /**
     * Creates a new instance of the AgreementAccessRepository.
     *
     * @return \AgreementAccessRepository
     */
    protected function _createAccessRepository()
    {
        if (null === $this->accessRepository) {
            $this->accessRepository = MainFactory::create(AgreementAccessRepository::class,
                                                          $this->_createFactory(),
                                                          $this->_createReader());
        }
        
        return $this->accessRepository;
    }
}