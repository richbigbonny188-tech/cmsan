<?php
/*--------------------------------------------------------------
   ProductVariantsDeleter.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\DeletionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class ProductVariantsDeleter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Data
 */
class ProductVariantsDeleter
{
    /**
     * ProductVariantsDeleter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection)
    {
    }


    /**
     * @param ProductVariantId ...$ids
     *
     * @throws DeletionOfProductVariantsFailed
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteProductVariants(ProductVariantId ...$ids): void
    {
        try {
            $this->connection->beginTransaction();
            $ids = array_map(fn(ProductVariantId $id): int => $id->value(), $ids);
            $this->deleteCombinationsFromProduct($ids);

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw DeletionOfProductVariantsFailed::becauseOfException($exception);
        }
    }


    /**
     * @param int[] $ids
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function deleteCombinationsFromProduct(array $ids): void
    {
        // storing product ids associated with given variants
        $productIdsResult = $this->connection->createQueryBuilder()
            ->distinct()
            ->select('products_id')
            ->from('products_properties_combis')
            ->where('products_properties_combis_id IN (' . implode(',', $ids) . ')')
            ->executeQuery()
            ->fetchAllAssociative();
        $productIds = array_column($productIdsResult, 'products_id');
        $this->connection->createQueryBuilder()
            ->delete('products_properties_combis')
            ->where('products_properties_combis_id IN (' . implode(',', $ids) . ')')
            ->executeQuery();
        $this->connection->createQueryBuilder()
            ->delete('products_properties_combis_values')
            ->where('products_properties_combis_id IN (' . implode(',', $ids) . ')')
            ->executeQuery();
        $this->connection->createQueryBuilder()
            ->delete('product_image_list_combi')
            ->where('products_properties_combis_id IN (' . implode(',', $ids) . ')')
            ->executeQuery();
        $this->connection->createQueryBuilder()
            ->delete('products_properties_index')
            ->where('products_properties_combis_id IN (' . implode(',', $ids) . ')')
            ->executeQuery();
        // checking all associated products if they still contain at least 1 variant
        foreach ($productIds as $productId) {

            $query = <<<SQL
                SELECT COUNT(`ppc`.`products_properties_combis_id`) AS 'combi_count'
                FROM `products_properties_combis` AS `ppc`
                WHERE `ppc`.`products_id` = $productId
            SQL;
            $stmt   = $this->connection->prepare($query);
            $result = $stmt->executeQuery();

            if ($result->rowCount() !== 0) {
                $combiCountForProduct = (int)$result->fetchNumeric()[0];

                if ($combiCountForProduct === 0) {
                    //  if no variant remained the configuration "use_properties_combis_quantity"
                    //  of product table will be rolled back to default value (0)
                    $this->connection->createQueryBuilder()
                        ->update('products')
                        ->set('use_properties_combis_quantity', 0)
                        ->where('products_id = :product_id')
                        ->setParameter('product_id', $productId)
                        ->executeQuery();
                }
            }
        }
    }


    /**
     * @description deletes all entries for $productId in  products_properties_admin_select
     *
     * @param ProductId $productId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteAdminSelectDataForProduct(ProductId $productId): void
    {
        $this->connection->createQueryBuilder()
            ->delete('products_properties_admin_select')
            ->where('products_id = :products_id')
            ->setParameter('products_id', $productId->value())
            ->executeQuery();
    }


    /**
     * @param ProductId $productId
     *
     * @throws DeletionOfProductVariantsFailed
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteAllProductVariantsByProductId(ProductId $productId): void
    {
        try {

            $this->connection->beginTransaction();

            // reading all variant ids assigned to product id
            $variantIds = $this->connection->createQueryBuilder()
                ->select('products_properties_combis_id')
                ->from('products_properties_combis')
                ->where('products_id = :products_id')
                ->setParameter('products_id', $productId->value())
                ->executeQuery()
                ->fetchAllAssociative();

            $variantIds = array_map(static function (array $row): int {
                return (int)$row['products_properties_combis_id'];
            }, $variantIds);
            $this->deleteCombinationsFromProduct($variantIds);
            $this->connection->commit();

        } catch (Exception $exception) {

            $this->connection->rollBack();
            throw DeletionOfProductVariantsFailed::becauseOfException($exception);
        }
    }
    /**
     * @param ProductId $productId
     * @param int $optionId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteAdminSelectDataForProductAndOption(ProductId $productId, int $optionId): void
    {
        $this->connection->createQueryBuilder()
            ->delete('products_properties_admin_select')
            ->where('products_id = :products_id')
            ->setParameter('products_id', $productId->value())
            ->andWhere('properties_id = :properties_id')
            ->setParameter('properties_id', $optionId)
            ->executeQuery();
    }
}