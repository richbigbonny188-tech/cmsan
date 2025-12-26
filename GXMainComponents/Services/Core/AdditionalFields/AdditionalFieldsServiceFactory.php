<?php
/*--------------------------------------------------------------
   AdditionalFieldsServiceFactory.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields;

use Doctrine\DBAL\Connection;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\AdditionalFieldsReadService;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\AdditionalFieldsRepository;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\AdditionalFieldsWriteService;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\Data\AdditionalFieldsMapper;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\Data\AdditionalFieldsReader;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\Data\AdditionalFieldsWriter;
use Gambio\MainComponents\Services\Core\AdditionalFields\App\Data\FieldKeyGenerator;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsReadService as AdditionalFieldsReadServiceInterface;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsRepository as AdditionalFieldsRepositoryInterface;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsWriteService as AdditionalFieldsWriteServiceInterface;
use \MainFactory;

/**
 * Class AdditionalFieldsServiceFactory
 * @package Gambio\MainComponents\Services\Core\AdditionalFields
 */
class AdditionalFieldsServiceFactory
{
    /**
     * @var Connection
     */
    protected $connection;
    
    /**
     * @var AdditionalFieldFactory|null
     */
    protected $factory;
    
    /**
     * @var AdditionalFieldsRepositoryInterface|null
     */
    protected $repository;
    
    /**
     * @var AdditionalFieldsWriteServiceInterface|null
     */
    protected $writeService;
    
    /**
     * @var AdditionalFieldsReadServiceInterface|null
     */
    protected $readService;
    
    
    /**
     * AdditionalFieldsServiceFactory constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @return AdditionalFieldsReadServiceInterface
     */
    public function createReadService(): AdditionalFieldsReadServiceInterface
    {
        if ($this->readService === null) {
    
            $this->readService = MainFactory::create(AdditionalFieldsReadService::class, $this->repository(), $this->factory());
        }
        
        return $this->readService;
    }
    
    
    /**
     * @return AdditionalFieldsWriteServiceInterface
     */
    public function createWriteService(): AdditionalFieldsWriteServiceInterface
    {
        if ($this->writeService === null) {
            
            $this->writeService = MainFactory::create(AdditionalFieldsWriteService::class, $this->repository(), $this->factory());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * @return AdditionalFieldsRepositoryInterface
     */
    protected function repository(): AdditionalFieldsRepositoryInterface
    {
        if ($this->repository === null) {
    
            $this->repository = MainFactory::create(AdditionalFieldsRepository::class,
                MainFactory::create(AdditionalFieldsMapper::class),
                MainFactory::create(AdditionalFieldsReader::class, $this->connection),
                MainFactory::create(AdditionalFieldsWriter::class, $this->connection, MainFactory::create(FieldKeyGenerator::class))
            );
        
        }
        return $this->repository;
    }
    
    
    /**
     * @return AdditionalFieldFactory
     */
    public function factory(): AdditionalFieldFactory
    {
        if ($this->factory === null) {
    
            $this->factory = MainFactory::create(AdditionalFieldFactory::class);
        }
        
        return $this->factory;
    }
}