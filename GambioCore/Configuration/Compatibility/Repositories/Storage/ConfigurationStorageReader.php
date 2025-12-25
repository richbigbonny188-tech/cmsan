<?php
/* --------------------------------------------------------------
 ConfigurationStorageReader.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Compatibility\Repositories\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use function str_replace;

/**
 * Class ConfigurationStorageReader
 *
 * @package Gambio\Core\Configuration\Repositories\Legacy\Storage
 */
class ConfigurationStorageReader
{
    private const TABLE_NAME = 'gx_configurations';
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var string
     */
    private $namespace;
    
    
    /**
     * ConfigurationStorageReader constructor.
     *
     * @param Connection $connection
     * @param string     $namespace
     */
    public function __construct(Connection $connection, string $namespace)
    {
        $this->connection = $connection;
        $this->namespace  = $namespace;
    }
    
    
    /**
     * Fetches configuration value by given key.
     *
     * @param string $key
     *
     * @return string|null
     * @throws Exception
     */
    public function get(string $key): ?string
    {
        $qb            = $this->connection->createQueryBuilder();
        $namespacedKey = "{$this->namespace}/{$key}";
        
        $where     = "{$this->connection->quoteIdentifier('key')} = {$qb->createNamedParameter($namespacedKey)}";
        $statement = $qb->select($this->connection->quoteIdentifier('value'))
            ->from(self::TABLE_NAME)
            ->where($where)
            ->executeQuery();
        
        $result = $statement->fetchAssociative();
        
        return $result['value'] ?? null;
    }
    
    
    /**
     * Fetches all configuration values in namespace.
     * If $prefix is provided, only keys with matching prefix are fetched.
     *
     * @param string|null $prefix
     *
     * @return array
     * @throws Exception
     */
    public function getAll(string $prefix = null): array
    {
        $qb = $this->connection->createQueryBuilder();
        
        $searchKey = $prefix ? "{$this->namespace}/{$prefix}%" : "{$this->namespace}%";
        $keyQuoted = $this->connection->quoteIdentifier('key');
        
        $select = "{$keyQuoted}, {$this->connection->quoteIdentifier('value')}";
        $where  = "{$keyQuoted} LIKE {$qb->createNamedParameter($searchKey)}";
        
        $data = $qb->select($select)->from(self::TABLE_NAME)->where($where)->executeQuery()->fetchAllAssociative();
        
        $configurations = [];
        $namespaceCut   = "{$this->namespace}/";
        
        foreach ($data as $dataSet) {
            $configKey                  = str_replace($namespaceCut, '', $dataSet['key']);
            $configurations[$configKey] = $dataSet['value'];
        }
        
        return $configurations;
    }
    
    
    /**
     * Checks if given key represents boolean true.
     *
     * @param string $key
     *
     * @return bool
     * @throws Exception
     */
    public function is(string $key): bool
    {
        $value = $this->get($key);
        
        return $value === 'true' || $value === '1';
    }
}