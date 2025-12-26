<?php
/*--------------------------------------------------------------
   CurrencyReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Currency\Model\ValueObjects\CurrencyId;
use Gambio\Admin\Modules\Currency\Services\Exceptions\CurrencyDoesNotExistException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class CurrencyReader
 *
 * @package Gambio\Admin\Modules\Currency\App\Data
 */
class CurrencyReader
{
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @param Filters|null $filters
     *
     * @return QueryBuilder
     */
    private function createQuery(?Filters $filters = null): QueryBuilder
    {
        $columns = $orderBy = [
            'c.currencies_id',
            'c.title',
            'c.code',
            'c.symbol_left',
            'c.symbol_right',
            'c.decimal_point',
            'c.thousands_point',
            'c.decimal_places',
            'c.value',
        ];
        
        $columns[] = 'IF(gxc.value=c.code, "true", "false") AS is_default';
        $orderBy[] = 'gxc.value';
        
        $joinCondition = 'gxc.key="configuration/DEFAULT_CURRENCY"';
        
        if ($filters !== null) {
            foreach ($filters->filters() as $filter) {
                if ($filter->attribute() === 'isDefault') {
                    $joinCondition .= $filter->value()
                                      === 'true' ? ' AND gxc.value = c.code' : ' AND gxc.value != c.code';
                    break;
                }
            }
        }
        
        return $this->connection->createQueryBuilder()
            ->select(implode(',', $columns))
            ->from('currencies', 'c')
            ->innerJoin('c', 'gx_configurations', 'gxc', $joinCondition)
            ->groupBy(implode(',', $orderBy));
    }
    
    
    /**
     * Returns a filtered, sorted, paginated collection of currencies.
     *
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function filterCurrencies(
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
    ): array {
        $query = $this->createQuery($filters);
        
        $filters->applyToQuery($query);
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * Returns the total count of filtered currencies.
     *
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getCurrenciesTotalCount(Filters $filters): int
    {
        $query = $this->createQuery($filters);
        
        $filters->applyToQuery($query);
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * Returns a specific currency based on the given currency ID.
     *
     * @param CurrencyId $currencyId
     *
     * @return array
     *
     * @throws CurrencyDoesNotExistException
     * @throws Exception
     */
    public function getCurrencyById(CurrencyId $currencyId): array
    {
        $result = $this->createQuery()
            ->where('currencies_id=:currencies_id')
            ->setParameter('currencies_id', $currencyId->value())
            ->executeQuery();
        
        if ($result->rowCount() === 0) {
            throw CurrencyDoesNotExistException::forCurrencyId($currencyId);
        }
        
        return $result->fetchAllAssociative();
    }
    
    
    /**
     * Returns a collection of all currencies.
     *
     * @return array
     * @throws Exception
     */
    public function getAllCurrencies(): array
    {
        return $this->createQuery()->executeQuery()->fetchAllAssociative();
    }
}