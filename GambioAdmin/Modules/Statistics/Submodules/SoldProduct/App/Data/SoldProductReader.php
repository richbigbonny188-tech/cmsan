<?php
/*--------------------------------------------------------------
   SoldProductReader.php 2023-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\Exceptions\SalesRecordNotFoundException;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class SoldProductReader
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data
 */
class SoldProductReader
{
    /**
     * SoldProductReader constructor.
     *
     * @param Connection      $connection
     * @param UserPreferences $userPreferences
     */
    public function __construct(
        private Connection      $connection,
        private UserPreferences $userPreferences
    ) {
    }
    
    
    /**
     * @param SqlPagination $pagination
     *
     * @return array
     */
    public function getSoldProducts(SqlPagination $pagination): array
    {
        $query = $this->createQuery();
        $pagination->applyToQuery($query);
        $soldProducts = $query->executeQuery()->fetchAllAssociative();
        
        foreach ($soldProducts as $index => $product) {
            $soldProducts[$index] = $this->addCategoriesToSoldProduct($product);
        }
        
        return $soldProducts;
    }
    
    
    /**
     * @return int
     */
    public function getSoldProductsTotalCount(): int
    {
        return $this->createQuery()->executeQuery()->rowCount();
    }
    
    
    /**
     * @param ProductId $productId
     *
     * @return array
     * @throws SalesRecordNotFoundException
     */
    public function getSoldProductByProductId(ProductId $productId): array
    {
        $result = $this->createQuery()
            ->andWhere('orders_products.products_id = :products_id')
            ->setParameter('products_id', $productId->value())
            ->executeQuery();
        
        if ($result->rowCount() === 0) {
            throw SalesRecordNotFoundException::create($productId);
        }
        
        $soldProduct = $result->fetchAssociative();
        $soldProduct = $this->addCategoriesToSoldProduct($soldProduct);
        
        return $soldProduct;
    }
    
    
    /**
     * Adds the categories to the sold product in which the product belongs to
     *
     * @param array $soldProduct
     *
     * @return array
     */
    private function addCategoriesToSoldProduct(array $soldProduct): array
    {
        if (!count($soldProduct) || !isset($soldProduct['products_id'])) {
            return $soldProduct;
        }
        
        $soldProduct['categories_id']   = 0;
        $soldProduct['categories_name'] = '';
        
        $result = $this->connection->createQueryBuilder()
            ->select('GROUP_CONCAT(COALESCE(cd.categories_name, \'Top\') ORDER BY cd.categories_id SEPARATOR \', \') AS categories_names')
            ->from('products_to_categories', 'ptc')
            ->leftJoin('ptc',
                       'categories_description',
                       'cd',
                       'cd.categories_id = ptc.categories_id AND cd.language_id = :language_id')
            ->where('ptc.products_id = :products_id')
            ->setParameter('language_id', $this->userPreferences->languageId())
            ->setParameter('products_id', $soldProduct['products_id'])
            ->groupBy('products_id')
            ->executeQuery();
        
        if ($result->rowCount() > 0) {
            $category                       = $result->fetchAssociative();
            $soldProduct['categories_name'] = $category['categories_names'];
        }
        
        return $soldProduct;
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function createQuery(): QueryBuilder
    {
        $columns = [
            'orders_products.products_id',
            'SUM(orders_products.products_quantity) AS products_ordered',
            'COALESCE(products_description.products_name, orders_products.products_name) AS products_name',
        ];
        
        return $this->connection->createQueryBuilder()
            ->select(implode(',', $columns))
            ->from('orders_products', 'orders_products')
            ->leftJoin('orders_products',
                       'products_description',
                       'products_description',
                       'products_description.products_id = orders_products.products_id AND products_description.language_id = :language_id')
            ->setParameter('language_id', $this->userPreferences->languageId())
            ->groupBy('orders_products.products_id', 'products_name')
            ->orderBy('products_ordered', 'DESC')
            ->addOrderBy('products_name');
    }
}