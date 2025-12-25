<?php
/*--------------------------------------------------------------------
 ProductOptionDeleter.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\DeletionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductOptionDeleter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Data
 */
class ProductDownloadDeleter
{
    /**
     * ProductOptionDeleter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }
    
    
    /**
     * @param AdditionalOptionId ...$ids
     *
     * @throws DeletionOfProductDownloadsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteProductOptions(AdditionalOptionId ...$ids): void
    {
        try {
            $this->connection->beginTransaction();
            
            $callback = static fn(AdditionalOptionId $id): int => $id->value();
            $ids      = array_map($callback, $ids);
            
            $this->connection->createQueryBuilder()
                ->delete('products_attributes')
                ->where('products_attributes_id IN (' . implode(',', $ids) . ')')
                ->executeQuery();
            
            $this->connection->createQueryBuilder()
                ->delete('product_image_list_attribute')
                ->where('products_attributes_id IN (' . implode(',', $ids) . ')')
                ->executeQuery();

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw DeletionOfProductDownloadsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ProductId $productId
     *
     * @throws DeletionOfProductDownloadsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteAllProductDownloadsByProductId(ProductId $productId): void
    {
        try {
            $this->connection->beginTransaction();
            
            $attributeIdsSubQuery = '
                SELECT `products_attributes_id`
                FROM products_attributes WHERE `products_id` = ' . $productId->value();

            $this->connection->createQueryBuilder()
                ->delete('product_image_list_attribute')
                ->where('products_attributes_id IN (' . $attributeIdsSubQuery . ')')
                ->executeQuery();

            $this->connection->createQueryBuilder()
                ->delete('products_attributes')
                ->where('products_id = :products_id')
                ->setParameter('products_id', $productId->value())
                ->executeQuery();

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw DeletionOfProductDownloadsFailedException::becauseOfException($exception);
        }
    }
}