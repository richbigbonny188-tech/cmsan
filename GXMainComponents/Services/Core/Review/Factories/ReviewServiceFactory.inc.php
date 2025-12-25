<?php

/* --------------------------------------------------------------
   ReviewServiceFactory.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewServiceFactory
 *
 * @category   System
 * @package    Review
 * @subpackage Factories
 */
class ReviewServiceFactory
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \ReviewFactory
     */
    protected $factory;
    
    /**
     * @var \ReviewAccessRepository
     */
    protected $accessRepository;
    
    /**
     * @var \ReviewRepository
     */
    protected $repository;
    
    /**
     * @var \ReviewReader
     */
    protected $reader;
    
    /**
     * @var \ReviewWriter
     */
    protected $writer;
    
    /**
     * @var \ReviewDeleter
     */
    protected $deleter;
    
    /**
     * @var \ReviewReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var \ReviewWriteServiceInterface
     */
    protected $writeService;
    
    
    /**
     * ReviewServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $query_builder
     */
    public function __construct(CI_DB_query_builder $query_builder)
    {
        $this->queryBuilder = $query_builder;
    }
    
    
    /**
     * Creates a review write service.
     *
     * @return \ReviewWriteServiceInterface
     */
    public function createWriteService()
    {
        if (null === $this->writeService) {
            $this->writeService = MainFactory::create(ReviewWriteService::class, $this->_createFactory());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * Creates a review read service.
     *
     * @return \ReviewReadServiceInterface
     */
    public function createReadService()
    {
        if (null === $this->readService) {
            $this->readService = MainFactory::create(ReviewReadService::class, $this->_createAccessRepository());
        }
        
        return $this->readService;
    }
    
    
    /**
     * Creates a new instance of ReviewAccessRepository.
     *
     * @return \ReviewAccessRepository
     */
    protected function _createAccessRepository()
    {
        if (null === $this->accessRepository) {
            $this->accessRepository = MainFactory::create(ReviewAccessRepository::class,
                                                          $this->_createFactory(),
                                                          $this->_createReader());
        }
        
        return $this->accessRepository;
    }
    
    
    /**
     * Creates a new instance of ReviewReader.
     * Consecutive calls provide the same object.
     *
     * @return \ReviewReader
     */
    protected function _createReader()
    {
        if (null === $this->reader) {
            $this->reader = MainFactory::create('ReviewReader', $this->queryBuilder);
        }
        
        return $this->reader;
    }
    
    
    /**
     * Creates a new instance of ReviewWriter.
     * Consecutive calls provide the same object.
     *
     * @return \ReviewWriter
     */
    protected function _createWriter()
    {
        if (null === $this->writer) {
            $this->writer = MainFactory::create('ReviewWriter', $this->queryBuilder);
        }
        
        return $this->writer;
    }
    
    
    /**
     * Creates a new instance of ReviewDeleter.
     * Consecutive calls provide the same object.
     *
     * @return \ReviewDeleter
     */
    protected function _createDeleter()
    {
        if (null === $this->deleter) {
            $this->deleter = MainFactory::create('ReviewDeleter', $this->queryBuilder);
        }
        
        return $this->deleter;
    }
    
    
    /**
     * Creates a new instance of ReviewRepository.
     * Consecutive calls provide the same object.
     *
     * @return \ReviewRepository
     */
    protected function _createRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create('ReviewRepository',
                                                    $this->_createWriter(),
                                                    $this->_createDeleter());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Creates a new instance of ReviewFactory
     * Consecutive calls provide the same object.
     *
     * @return \ReviewFactory
     */
    protected function _createFactory()
    {
        if (null === $this->factory) {
            $this->factory = MainFactory::create('ReviewFactory', $this->_createRepository());
        }
        
        return $this->factory;
    }
}
