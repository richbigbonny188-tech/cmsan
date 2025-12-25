<?php
/*--------------------------------------------------------------
   AdditionalFieldsReader.php 2023-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\AdditionalFieldDoesNotExistException;

/**
 * Class AdditionalFieldsReader
 *
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\App\Data
 */
class AdditionalFieldsReader
{
    /**
     * @var Connection
     */
    protected $connection;
    
    
    /**
     * AdditionalFieldsReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @param FieldId $fieldId
     *
     * @return array
     *
     * @throws AdditionalFieldDoesNotExistException
     * @throws Exception
     */
    public function getAdditionalFieldById(FieldId $fieldId): array
    {
        $result = $this->createQuery()
            ->where('af.additional_field_id = :additional_field_id')
            ->setParameter('additional_field_id', $fieldId->value());
        $result = $result->executeQuery();
        
        if ($result->rowCount() === 0) {
            throw AdditionalFieldDoesNotExistException::forFieldId($fieldId);
        }
        
        return $this->mapResultData($result->fetchAllAssociative());
    }
    
    
    /**
     * Returns a collection of all additional fields.
     *
     * @param int|null    $limit
     * @param int|null    $offset
     * @param string|null $searchTerm
     * @param array|null  $sortBy
     *
     * @return array
     * @throws Exception
     */
    public function getAllAdditionalFields(
        ?int    $limit = null,
        ?int    $offset = null,
        ?string $searchTerm = null,
        ?array  $sortBy = null
    ): array {
        $query = $this->createQuery(['af.additional_field_id']);
        
        if ($limit !== null) {
            $query->setMaxResults($limit);
        }
        
        if ($offset !== null) {
            $query->setFirstResult($offset);
        }
        
        if ($searchTerm !== null) {
            $query->where('afd.name LIKE :name')->setParameter('name', '%' . addcslashes($searchTerm, '%_') . '%');
        }
        
        $sortBy = $sortBy ?? [];
        $this->applySorting($query, $sortBy);
        
        $ids = $query->executeQuery()->fetchAllAssociative();
        $ids = array_map(static function (array $data): int {
            return (int)$data['additional_field_id'];
        }, $ids);
        
        if (count($ids) === 0) {
            return [];
        }
        
        $result = $this->createQuery()->where('af.additional_field_id IN (' . implode(', ', $ids) . ')');
        
        $this->applySorting($result, $sortBy);
        
        $result = $result->executeQuery()->fetchAllAssociative();
        
        return $this->mapResultData($result);
    }
    
    
    /**
     * @param QueryBuilder $builder
     * @param array        $columns
     */
    protected function applySorting(QueryBuilder $builder, array $columns): void
    {
        if (count($columns)) {
            foreach ($columns as $index => $parameters) {
                $method = $index === 0 ? 'orderBy' : 'addOrderBy';
                [$column, $orderKeyWord] = $parameters; #e.g ['af.additional_field_id', 'DESC']
                
                $builder->$method($column, $orderKeyWord);
            }
        }
    }
    
    
    /**
     * @param array|null $columns
     *
     * @return QueryBuilder
     */
    protected function createQuery(array $columns = null): QueryBuilder
    {
        $columns = $columns ?? [
            'af.additional_field_id',
            'afd.name',
            'l.code',
        ];
        
        return $this->connection->createQueryBuilder()
            ->select(implode(', ', $columns))
            ->from('additional_fields', 'af')
            ->innerJoin('af', 'additional_field_descriptions', 'afd', 'af.additional_field_id=afd.additional_field_id')
            ->innerJoin('afd', 'languages', 'l', 'l.languages_id=afd.language_id')
            ->groupBy(implode(', ', $columns));
    }
    
    
    /**
     * @param array $rows
     *
     * @return array
     */
    protected function mapResultData(array $rows): array
    {
        $result = [];
        
        foreach ($rows as $row) {
            $fieldId      = (int)$row['additional_field_id'];
            $languageCode = $row['code'];
            
            $result[$fieldId][$languageCode] = $row['name'];
        }
        
        return $result;
    }
}