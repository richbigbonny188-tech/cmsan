<?php
/* --------------------------------------------------------------
   ProductsMappingRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AfterbuyProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ProductMapping;

/*
 * CREATE TABLE `afterbuy_products` (
 `products_id` int NOT NULL,
 `combi_id` int DEFAULT NULL,
 `afterbuy_product_id` int NOT NULL,
 `data_origin` varchar(10) NOT NULL DEFAULT '',
 UNIQUE KEY `afterbuy_product_id` (`afterbuy_product_id`),
 UNIQUE KEY `products_id_2` (`products_id`,`combi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
 */

/**
 * Class ProductsMappingRepository
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products
 */
class ProductsMappingRepository
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Adds a mapping to the repository.
     *
     * $dataOrigin may be 'unknown', 'shop' (product originally created in shop, exported to Afterbuy) or
     * 'afterbuy' (product originally created in Afterbuy, imported into shop)
     *
     * @throws Exception
     */
    public function addMapping(ProductMapping $mapping, string $dataOrigin = 'unknown'): void
    {
        $data = [
            'products_id'         => $mapping->getProductsId(),
            'combi_id'            => $mapping->getCombiId(),
            'afterbuy_product_id' => $mapping->getAfterbuyProductId(),
            'data_origin'         => $dataOrigin,
        ];
        $this->connection->insert('afterbuy_products', $data);
    }
    
    
    /**
     * @param int      $productsId
     * @param int|null $combiId
     *
     * @return ProductMapping
     * @throws Exception
     */
    public function findMappingByProductsId(int $productsId, ?int $combiId): ProductMapping
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query        = $queryBuilder->select('*')->from('afterbuy_products');
        if ($combiId === null) {
            $query->where('products_id = ' . $queryBuilder->createNamedParameter($productsId)
                          . ' AND combi_id IS NULL');
        } else {
            $query->where('products_id = ' . $queryBuilder->createNamedParameter($productsId) . ' AND combi_id = '
                          . $queryBuilder->createNamedParameter($combiId));
        }
        $result = $query->executeQuery()->fetchAllAssociative();
        if (count($result) === 1) {
            return new ProductMapping((int)$result[0]['products_id'],
                                      (int)$result[0]['combi_id'],
                                      (int)$result[0]['afterbuy_product_id']);
        }
        
        return new ProductMapping();
    }
    
    
    /**
     * @throws Exception
     */
    public function findMappingByAfterbuyProduct(AfterbuyProduct $afterbuyProduct): ProductMapping
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $result       = $queryBuilder->select('*')
            ->from('afterbuy_products')
            ->where('afterbuy_product_id = ?')
            ->setParameter(0, $afterbuyProduct->getProductID())
            ->executeQuery()
            ->fetchAllAssociative();
        if (count($result) === 1) {
            if ($this->isValidProduct((int)$result[0]['products_id'])) {
                return new ProductMapping((int)$result[0]['products_id'],
                                          (int)$result[0]['combi_id'],
                                          (int)$result[0]['afterbuy_product_id']);
            }
            $this->removeMappingsForProduct((int)$result[0]['products_id']);
        }
        
        return new ProductMapping();
    }
    
    
    /**
     * @param int $productsId
     *
     * @return bool
     * @throws Exception
     */
    private function isValidProduct(int $productsId): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query        = $queryBuilder->select('products_id')
            ->from('products')
            ->where('products_id = ?')
            ->setParameter(0, $productsId);
        $result       = $query->executeQuery()->fetchAllAssociative();
        
        return !empty($result);
    }
    
    
    /**
     * @param int $productsId
     *
     * @return int
     * @throws Exception
     */
    public function removeMappingsForProduct(int $productsId): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query        = $queryBuilder->where('products_id = ?')
            ->setParameter(0, $productsId)
            ->delete('afterbuy_products');
        $rowsDeleted  = $query->executeStatement();
        
        return $rowsDeleted;
    }
}

