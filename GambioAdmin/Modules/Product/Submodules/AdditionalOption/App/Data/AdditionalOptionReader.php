<?php
/*--------------------------------------------------------------------
 ProductOptionReader.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionDoesNotExistException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class AdditionalOptionReader
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data
 */
class AdditionalOptionReader
{
    /**
     * AdditionalOptionReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }
    
    
    /**
     * @return QueryBuilder
     */
    private function getQuery(): QueryBuilder
    {
        $selectFields            = [
            'pa.products_attributes_id',
            'pa.products_id',
            'po.options_id',
            'pov.option_value_id',
            'pila.product_image_list_id',
            'pa.attributes_model',
            'pa.weight_prefix',
            'pa.options_values_weight',
            'pa.price_prefix',
            'pa.options_values_price',
            'pa.attributes_stock',
            'pa.stock_type',
            'pa.sortorder',
        ];
        $commaSeparatedFieldList = implode(',', $selectFields);
        
        return $this->connection->createQueryBuilder()
            ->select($commaSeparatedFieldList)
            ->from('products_attributes', 'pa')
            ->leftJoin('pa', 'products_options_values', 'pov', 'pov.products_options_values_id=pa.options_values_id')
            ->leftJoin('pa',
                       'product_image_list_attribute',
                       'pila',
                       'pa.products_attributes_id=pila.products_attributes_id')
            ->leftJoin('pa', 'products_options', 'po', 'po.products_options_id=pa.options_id')
            ->groupBy($commaSeparatedFieldList)
            ->orderBy('pa.sortorder');
    }
    
    
    /**
     * @param int $productId
     *
     * @return array
     * @throws Exception
     */
    public function getAdditionalOptionsByProductId(int $productId): array
    {
        return $this->getQuery()
            ->where('pa.products_id = :products_id')
            ->setParameter('products_id', $productId)
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param int $productId
     *
     * @return array
     * @throws Exception
     */
    public function getAdditionalOptionIdsByProductId(int $productId): array
    {
        $result = $this->getQuery()
            ->select('pa.products_attributes_id')
            ->where('pa.products_id = :products_id')
            ->setParameter('products_id', $productId)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $callback = static fn(array $row): int => (int)$row['products_attributes_id'];
        return array_map($callback, $result);
    }
    
    
    /**
     * @param int $productOptionId
     *
     * @return array
     * @throws Exception
     * @throws AdditionalOptionDoesNotExistException
     */
    public function getAdditionalOptionById(int $productOptionId): array
    {
        $result = $this->getQuery()
            ->where('pa.products_attributes_id = :products_attributes_id')
            ->setParameter('products_attributes_id', $productOptionId)
            ->executeQuery()
            ->fetchAllAssociative();
        
        if (empty($result)) {
            throw AdditionalOptionDoesNotExistException::forAdditionalOptionId($productOptionId);
        }
        
        return $result[0];
    }
    
    
    /**
     * @param ProductId                $productId
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function filterAdditionalOptions(
        ProductId                $productId,
        Filters|SqlFilters       $filters,
        Sorting|SqlSorting       $sorting,
        Pagination|SqlPagination $pagination
    ): array {
        $queryBuilder = $this->getQuery()
            ->where('pa.products_id = :products_id')
            ->setParameter('products_id', $productId->value());
        
        $filters->applyToQuery($queryBuilder);
        $sorting->applyToQuery($queryBuilder);
        $pagination->applyToQuery($queryBuilder);
        
        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * @param ProductId          $productId
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getAdditionalOptionsTotalCount(ProductId $productId, Filters|SqlFilters $filters): int
    {
        $queryBuilder = $this->getQuery()
            ->where('pa.products_id = :products_id')
            ->setParameter('products_id', $productId->value());
        
        $filters->applyToQuery($queryBuilder);
        
        return $queryBuilder->executeQuery()->rowCount();
    }
    
    
    /**
     * @param int ...$optionIds
     *
     * @return bool
     * @throws Exception
     */
    public function isOneOrMoreOptionsInUse(int ...$optionIds): bool
    {
        if (count($optionIds) === 0) {
            return false;
        }
        
        return $this->connection->createQueryBuilder()
                   ->select('products_attributes_id')
                   ->from('products_attributes', 'pa')
                   ->innerJoin('pa', 'products_options', 'po', 'po.products_options_id=pa.options_id')
                   ->where('po.options_id IN (' . implode(', ', $optionIds) . ')')
                   ->executeQuery()
                   ->rowCount() > 0;
    }
    
    
    /**
     * @param int $optionId
     * @param int ...$optionValueIds
     *
     * @return bool
     * @throws Exception
     */
    public function areDifferentOptionValuesInUse(int $optionId, int ...$optionValueIds): bool
    {
        $query = $this->getQuery()->where('po.options_id = :optionId')->setParameter('optionId', $optionId);

        if (count($optionValueIds) > 0) {
            $query->andWhere('pov.option_value_id  NOT IN (' . implode(', ', $optionValueIds) . ')');
        }

        return $query->executeQuery()->rowCount() > 0;
    }
    
    
    /**
     * @param int ...$imageListIds
     *
     * @return bool
     * @throws Exception
     */
    public function imageListsAreAssignedToAnAdditionalOption(int ...$imageListIds): bool
    {
        return $this->connection->createQueryBuilder()
                   ->select('products_attributes_id')
                   ->from('product_image_list_attribute')
                   ->where('product_image_list_id IN (' . implode(', ', $imageListIds) . ')')
                   ->executeQuery()
                   ->rowCount() > 0;
    }
}