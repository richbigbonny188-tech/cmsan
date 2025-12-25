<?php
/* --------------------------------------------------------------
 ConfigurationStorageRepositoryBuilder.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 15 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Compatibility;

use Doctrine\DBAL\Connection;
use Gambio\Core\Configuration\Compatibility\Repositories\ConfigurationStorageDoctrineRepository;
use Gambio\Core\Configuration\Compatibility\Repositories\Storage\ConfigurationStorageReader;
use Gambio\Core\Configuration\Compatibility\Repositories\Storage\ConfigurationStorageWriter;
use Gambio\Core\Configuration\Compatibility\Repositories\Storage\NamespaceConverter;

/**
 * Class ConfigurationStorageRepositoryBuilder
 * @package Gambio\Core\Configuration\Compatibility
 *
 * @codeCoverageIgnore
 */
class ConfigurationStorageRepositoryBuilder
{
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var NamespaceConverter
     */
    private $namespaceConverter;
    
    
    /**
     * ConfigurationStorageRepositoryBuilder constructor.
     *
     * @param Connection         $connection
     * @param NamespaceConverter $namespaceConverter
     */
    public function __construct(Connection $connection, NamespaceConverter $namespaceConverter)
    {
        $this->connection         = $connection;
        $this->namespaceConverter = $namespaceConverter;
    }
    
    
    /**
     * Builds a configuration repository for the given namespace.
     *
     * @param string $namespace
     *
     * @return ConfigurationStorageRepository
     */
    public function build(string $namespace): ConfigurationStorageRepository
    {
        $reader = new ConfigurationStorageReader($this->connection, $namespace);
        $writer = new ConfigurationStorageWriter($this->connection, $namespace);
        
        return new ConfigurationStorageDoctrineRepository($reader, $writer, $this->namespaceConverter);
    }
}