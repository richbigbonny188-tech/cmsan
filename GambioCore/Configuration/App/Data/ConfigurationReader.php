<?php
/* --------------------------------------------------------------
 ConfigurationReader.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use function implode;

/**
 * Class ConfigurationReader
 *
 * @package Gambio\Core\Configuration\Repositories\Components
 */
class ConfigurationReader
{
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * ConfigurationReader constructor.
     *
     * @param Connection $queryBuilder
     */
    public function __construct(Connection $queryBuilder)
    {
        $this->connection = $queryBuilder;
    }
    
    
    /**
     * Finds a configuration item by the given key.
     * Returns null if no matching item was found.
     *
     * @param string $key
     *
     * @return array|null
     * @throws Exception
     */
    public function find(string $key): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        
        $select = $this->createSelect(['key', 'value']) . ', sort_order as sortOrder';
        $where  = $this->connection->quoteIdentifier('key') . ' = ' . $qb->createNamedParameter($key);
        
        $qb->select($select)->from('gx_configurations')->where($where);
        $result = $qb->executeQuery()->fetchAssociative();
        
        return $result ? : null;
    }
    
    
    /**
     * Finds a language dependent configuration item by the given key and language code.
     * Returns null if nothing was found with given arguments.
     *
     * @param string $key
     * @param string $languageCode
     *
     * @return array|null
     * @throws Exception
     */
    public function findLanguageDependent(string $key, string $languageCode): ?array
    {
        $langId = $this->languageCodeToId($languageCode);
        if ($langId === null) {
            return null;
        }
        
        $qb         = $this->connection->createQueryBuilder();
        $configData = $qb->select("{$this->connection->quoteIdentifier('value')}, sort_order")
            ->from('gx_lang_configurations')
            ->where("{$this->connection->quoteIdentifier('key')} = {$qb->createNamedParameter($key)}")
            ->andWhere("language_id = {$qb->createNamedParameter($langId)}")
            ->executeQuery()
            ->fetchAssociative();
        
        if ($configData === false) {
            return null;
        }
        
        return [
            'key'          => $key,
            'languageCode' => $languageCode,
            'value'        => $configData['value'],
            'sortOrder'    => $configData['sort_order'],
        ];
    }
    
    
    /**
     * Checks if configuration value for given key is available.
     *
     * @param string $key
     *
     * @return bool
     * @throws Exception
     */
    public function has(string $key): bool
    {
        $qb = $this->connection->createQueryBuilder();
        
        $where = "{$this->connection->quoteIdentifier('key')} = {$qb->createNamedParameter($key)}";
        $qb->select('*')->from('gx_configurations')->where($where);
        
        return $qb->executeQuery()->rowCount() > 0;
    }
    
    
    /**
     * Checks if configuration value for given key and language code is available.
     *
     * @param string $key
     * @param string $languageCode
     *
     * @return bool
     * @throws Exception
     */
    public function hasLanguageDependent(string $key, string $languageCode): bool
    {
        $languageId = $this->languageCodeToId($languageCode);
        if (!$languageId) {
            return false;
        }
        
        $qb = $this->connection->createQueryBuilder();
        
        $where    = "{$this->connection->quoteIdentifier('key')} = {$qb->createNamedParameter($key)}";
        $andWhere = "language_id = {$qb->createNamedParameter($languageId)}";
        $result   = $qb->select('*')
            ->from('gx_lang_configurations')
            ->where($where)
            ->andWhere($andWhere)
            ->executeQuery();
        
        return $result->rowCount() > 0;
    }
    
    
    /**
     * Creates a select query string from the given fields.
     * Backticks are applied to the final returned string.
     *
     * @param array $selectedFields
     *
     * @return string
     */
    private function createSelect(array $selectedFields): string
    {
        $fields = [];
        
        foreach ($selectedFields as $selectedField) {
            $fields[] = $this->connection->quoteIdentifier($selectedField);
        }
        
        return implode(', ', $fields);
    }
    
    
    /**
     * Converts the language code into a language id.
     *
     * @param string $languageCode
     *
     * @return string|null
     * @throws Exception
     */
    private function languageCodeToId(string $languageCode): ?string
    {
        $qb       = $this->connection->createQueryBuilder();
        $langData = $qb->select('languages_id')
            ->from('languages')
            ->where("code = {$qb->createNamedParameter($languageCode)}")
            ->executeQuery()
            ->fetchAssociative();
        
        if ($langData === false) {
            return null;
        }
        
        return $langData['languages_id'];
    }
}