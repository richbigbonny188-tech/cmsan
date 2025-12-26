<?php
/*--------------------------------------------------------------------
 ProductOptionDeleter.php 20213-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 20213 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\DeletionOfAdditionalOptionsFailedException;

/**
 * Class AdditionalOptionDeleter
 *
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data
 */
class AdditionalOptionDeleter
{
    /**
     * AdditionalOptionDeleter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }
    
    
    /**
     * @param AdditionalOptionId ...$ids
     *
     * @throws DeletionOfAdditionalOptionsFailedException
     */
    public function deleteAdditionalOptions(AdditionalOptionId ...$ids): void
    {
        try {
            $this->connection->beginTransaction();
            
            $ids = array_map(static fn(AdditionalOptionId $id) => $id->value(), $ids);
            
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
            
            throw DeletionOfAdditionalOptionsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ProductId $productId
     *
     * @throws DeletionOfAdditionalOptionsFailedException
     */
    public function deleteAllAdditionalOptionsByProductId(ProductId $productId): void
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

            throw DeletionOfAdditionalOptionsFailedException::becauseOfException($exception);
        }
    }
}