<?php
/* --------------------------------------------------------------
   WithdrawalServiceFactory.inc.php 2018-01-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalServiceFactory
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Factories
 */
class WithdrawalServiceFactory
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \WithdrawalAccessRepository
     */
    protected $accessRepository;
    
    /**
     * @var \WithdrawalRepository
     */
    protected $repository;
    
    /**
     * @var \WithdrawalReaderInterface
     */
    protected $reader;
    
    /**
     * @var \WithdrawalWriterInterface
     */
    protected $writer;
    
    /**
     * @var \WithdrawalDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var \WithdrawalReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var \WithdrawalWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var \WithdrawalFactory
     */
    protected $factory;
    
    
    /**
     * WithdrawalServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates a withdrawal write service.
     *
     * @return \WithdrawalWriteServiceInterface
     */
    public function createWriteService()
    {
        if (null === $this->writeService) {
            $this->writeService = MainFactory::create(WithdrawalWriteService::class, $this->_createFactory());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * Creates a withdrawal read service.
     *
     * @return \WithdrawalReadServiceInterface
     */
    public function createReadService()
    {
        if (null === $this->readService) {
            $this->readService = MainFactory::create(WithdrawalReadService::class, $this->_createAccessRepository());
        }
        
        return $this->readService;
    }
    
    
    /**
     * Creates a new instance of a withdrawalRepository object.
     *
     * @return \WithdrawalAccessRepository
     */
    protected function _createAccessRepository()
    {
        if (null === $this->accessRepository) {
            $this->accessRepository = MainFactory::create('WithdrawalAccessRepository',
                                                          $this->_createFactory(),
                                                          $this->_createReader());
        }
        
        return $this->accessRepository;
    }
    
    
    /**
     * Creates a new instance of WithdrawalRepository.
     * Consecutive calls provide the same object.
     *
     * @return \WithdrawalRepositoryInterface
     */
    protected function _createRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create('WithdrawalRepository',
                                                    $this->_createWriter(),
                                                    $this->_createDeleter());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Creates a new instance of WithdrawalReader.
     * Consecutive calls provide the same object.
     *
     * @return \WithdrawalReaderInterface
     */
    protected function _createReader()
    {
        if (null === $this->reader) {
            $this->reader = MainFactory::create('WithdrawalReader', $this->queryBuilder);
        }
        
        return $this->reader;
    }
    
    
    /**
     * Creates a new instance of WithdrawalReader.
     * Consecutive calls provide the same object.
     *
     * @return \WithdrawalWriterInterface
     */
    protected function _createWriter()
    {
        if (null === $this->writer) {
            $this->writer = MainFactory::create('WithdrawalWriter', $this->queryBuilder);
        }
        
        return $this->writer;
    }
    
    
    /**
     * Creates a new instance of WithdrawalDeleter.
     * Consecutive calls provide the same object.
     *
     * @return \WithdrawalDeleterInterface
     */
    protected function _createDeleter()
    {
        if (null === $this->deleter) {
            $this->deleter = MainFactory::create('WithdrawalDeleter', $this->queryBuilder);
        }
        
        return $this->deleter;
    }
    
    
    /**
     * Creates a new instance of WithdrawalFactory
     * Consecutive calls provide the same object.
     *
     * @return \WithdrawalFactory
     */
    protected function _createFactory()
    {
        if (null === $this->factory) {
            $this->factory = MainFactory::create('WithdrawalFactory', $this->_createRepository());
        }
        
        return $this->factory;
    }
}
