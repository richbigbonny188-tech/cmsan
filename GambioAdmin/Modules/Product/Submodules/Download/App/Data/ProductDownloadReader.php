<?php
/*--------------------------------------------------------------------
 ProductDownloadReader.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadDoesNotExistException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class ProductDownloadReader
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Data
 */
class ProductDownloadReader
{
    /**
     * ProductDownloadReader constructor.
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProductOptionsByProductId(int $productId): array
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProductOptionIdsByProductId(int $productId): array
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
     * @throws ProductDownloadDoesNotExistException
     */
    public function getProductOptionById(int $productOptionId): array
    {
        $result = $this->getQuery()
            ->where('pa.products_attributes_id = :products_attributes_id')
            ->setParameter('products_attributes_id', $productOptionId)
            ->executeQuery()
            ->fetchAllAssociative();
        
        if (empty($result)) {
            throw ProductDownloadDoesNotExistException::forProductOptionId($productOptionId);
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function filterProductDownloads(
        ProductId  $productId,
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function getProductDownloadsTotalCount(ProductId $productId, Filters $filters): int
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
     * @throws \Doctrine\DBAL\Exception
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
     * @throws \Doctrine\DBAL\Exception
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function imageListsAreAssignedToAProductOption(int ...$imageListIds): bool
    {
        return $this->connection->createQueryBuilder()
                   ->select('products_attributes_id')
                   ->from('product_image_list_attribute')
                   ->where('product_image_list_id IN (' . implode(', ', $imageListIds) . ')')
                   ->executeQuery()
                   ->rowCount() > 0;
    }
}