<?php
/*--------------------------------------------------------------
   AdditionalProductFieldsServiceFactory.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields;

use Doctrine\DBAL\Connection;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\AdditionalProductFieldReadService;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\AdditionalProductFieldWriteService;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldReadService as AdditionalProductFieldReadServiceInterface;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldRepository as AdditionalProductFieldRepositoryInterface;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldWriteService as AdditionalProductFieldWriteServiceInterface;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\AdditionalProductFieldRepository;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data\AdditionalProductFieldsMapper;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data\AdditionalProductFieldsReader;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data\AdditionalProductFieldsWriter;
use MainFactory;

/**
 * Class AdditionalProductFieldsServiceFactory
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields
 */
class AdditionalProductFieldsServiceFactory
{
    /**
     * @var Connection
     */
    protected $connection;
    
    /**
     * @var AdditionalProductFieldReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var AdditionalProductFieldWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var AdditionalProductFieldRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var AdditionalProductFieldFactory
     */
    protected $factory;
    
    
    /**
     * AdditionalProductFieldsServiceFactory constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @return AdditionalProductFieldReadServiceInterface
     */
    public function createReadService(): AdditionalProductFieldReadServiceInterface
    {
        if ($this->readService === null) {
    
            $this->readService = MainFactory::create(AdditionalProductFieldReadService::class, $this->repository(), $this->factory());
        }
        
        return $this->readService;
    }
    
    
    /**
     * @return AdditionalProductFieldWriteServiceInterface
     */
    public function createWriteService(): AdditionalProductFieldWriteServiceInterface
    {
        if ($this->writeService === null) {
            
            $this->writeService = MainFactory::create(AdditionalProductFieldWriteService::class, $this->repository(), $this->factory());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * @return AdditionalProductFieldRepositoryInterface
     */
    protected function repository(): AdditionalProductFieldRepositoryInterface
    {
        if ($this->repository === null) {
    
            $mapper           = MainFactory::create(AdditionalProductFieldsMapper::class);
            $reader           = MainFactory::create(AdditionalProductFieldsReader::class, $this->connection);
            $writer           = MainFactory::create(AdditionalProductFieldsWriter::class, $this->connection);
            $this->repository = MainFactory::create(AdditionalProductFieldRepository::class, $mapper, $reader, $writer);
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return AdditionalProductFieldFactory
     */
    public function factory(): AdditionalProductFieldFactory
    {
        if ($this->factory === null) {
    
            $this->factory = MainFactory::create(AdditionalProductFieldFactory::class);
        }
        
        return $this->factory;
    }
}