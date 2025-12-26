<?php
/* --------------------------------------------------------------
  GmConfigurationServiceFactory.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class GmConfigurationServiceFactory
 */
class GmConfigurationServiceFactory implements GmConfigurationServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var GmConfigurationReaderInterface
     */
    protected $reader;
    
    /**
     * @var GmConfigurationRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var GmConfigurationFactoryInterface
     */
    protected $factory;
    
    /**
     * @var GmConfigurationServiceInterface
     */
    protected $service;
    
    /**
     * @var GmConfigurationWriterInterface
     */
    protected $writer;
    
    
    /**
     * GmConfigurationServiceFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @return GmConfigurationReaderInterface
     */
    public function reader(): GmConfigurationReaderInterface
    {
        if ($this->reader === null) {
            
            $this->reader = new GmConfigurationReader($this->queryBuilder);
        }
        
        return $this->reader;
    }
    
    
    /**
     * @return GmConfigurationRepositoryInterface
     */
    public function repository(): GmConfigurationRepositoryInterface
    {
        if ($this->repository === null) {
            
            $this->repository = new GmConfigurationRepository($this->reader(), $this->writer(), $this->factory());
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return GmConfigurationFactoryInterface
     */
    public function factory(): GmConfigurationFactoryInterface
    {
        if ($this->factory === null) {
            
            $this->factory = new GmConfigurationFactory;
        }
        
        return $this->factory;
    }
    
    
    /**
     * @return GmConfigurationServiceInterface
     */
    public function service(): GmConfigurationServiceInterface
    {
        if ($this->service === null) {
            $container     = LegacyDependencyContainer::getInstance();
            $this->service = new GmConfigurationService($container->get(ConfigurationService::class));
        }
    
        return $this->service;
    }
    
    
    /**
     * @return GmConfigurationWriterInterface
     */
    public function writer(): GmConfigurationWriterInterface
    {
        if ($this->writer === null) {
            
            $this->writer = new GmConfigurationWriter($this->queryBuilder);
        }
        
        return $this->writer;
    }
}