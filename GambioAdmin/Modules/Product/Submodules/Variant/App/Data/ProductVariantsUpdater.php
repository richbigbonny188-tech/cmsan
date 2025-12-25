<?php
/*--------------------------------------------------------------
   ProductVariantsUpdater.php 2023-06-27
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
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Traits\WeightAndPriceTypeValidator;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;

/**
 * Class ProductVariantsUpdater
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Data
 */
class ProductVariantsUpdater
{
    use WeightAndPriceTypeValidator;

    /**
     * ProductVariantsUpdater constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection){}


    /**
     * @param ProductVariant ...$productVariants
     *
     * @throws StorageOfProductVariantsFailed
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function storeProductVariants(ProductVariant ...$productVariants): void
    {
        try {
            $this->connection->beginTransaction();

            foreach ($productVariants as $productVariant) {
                $this->updateProductVariant($productVariant);
                $this->updateVariantCombinations($productVariant);
                $this->updateProductVariantImageList($productVariant);
                $this->indexCombiAndOptions($productVariant->id());
            }

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw StorageOfProductVariantsFailed::becauseOfException($exception);
        }
    }


    /**
     * @param ProductVariant $productVariant
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateProductVariant(ProductVariant $productVariant): void
    {
        $this->connection->createQueryBuilder()
            ->update('products_properties_combis')
            ->set('sort_order', ':sort_order')
            ->set('combi_model', ':combi_model')
            ->set('combi_ean', ':combi_ean')
            ->set('combi_quantity', ':combi_quantity')
            ->set('stock_type', ':stock_type')
            ->set('combi_shipping_status_id', ':combi_shipping_status_id')
            ->set('combi_weight', ':combi_weight')
            ->set('combi_weight_type', ':combi_weight_type')
            ->set('combi_price', ':combi_price')
            ->set('combi_price_type', ':combi_price_type')
            ->set('products_vpe_id', ':products_vpe_id')
            ->set('vpe_value', ':vpe_value')
            ->set('gtin', ':gtin')
            ->set('asin', ':asin')
            ->where('products_properties_combis_id = :products_properties_combis_id')
            ->setParameter('products_properties_combis_id', $productVariant->id())
            ->setParameter('sort_order', $productVariant->sortOrder())
            ->setParameter('combi_model', $productVariant->modelNumber())
            ->setParameter('combi_ean', $productVariant->ean())
            ->setParameter('combi_quantity', $productVariant->stock())
            ->setParameter('stock_type', $productVariant->stockType())
            ->setParameter('combi_shipping_status_id', $productVariant->deliveryTimeId())
            ->setParameter('combi_weight', $productVariant->weight())
            ->setParameter('combi_weight_type', $this->getDatabaseTypeFieldValue($productVariant->weightType()))
            ->setParameter('combi_price', $productVariant->price())
            ->setParameter('combi_price_type', $this->getDatabaseTypeFieldValue($productVariant->priceType()))
            ->setParameter('products_vpe_id', $productVariant->vpeUnitId() ?? 0)
            ->setParameter('vpe_value', $productVariant->vpeScalarValue())
            ->setParameter('gtin', $productVariant->gtin())
            ->setParameter('asin', $productVariant->asin())
            ->executeQuery();
    }


    /**
     * @param ProductVariant $productVariant
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateVariantCombinations(ProductVariant $productVariant): void
    {
        $this->connection->createQueryBuilder()
            ->delete('products_properties_combis_values')
            ->where('products_properties_combis_id = :variant_id')
            ->setParameter('variant_id', $productVariant->id())
            ->executeQuery();

        $insertAdminSelectData = [];
        $insertAdminSelectQuery = '
                    INSERT INTO `products_properties_admin_select`
                    (`products_id`, `properties_id`, `properties_values_id`) VALUES
                    ';

        foreach ($productVariant->combination() as $optionAndOptionValueId) {
            $subQuery = '(SELECT `properties_values_id` FROM `properties_values` '
                . 'WHERE `properties_values_id` = :option_value_id AND  `properties_id` = :option_id)';

            $this->connection->createQueryBuilder()
                ->insert('products_properties_combis_values')
                ->setValue('products_properties_combis_id', ':variant_id')
                ->setValue('properties_values_id', $subQuery)
                ->setValue('options_id', ':option_id')
                ->setParameter('variant_id', $productVariant->id())
                ->setParameter('option_id', $optionAndOptionValueId->optionId())
                ->setParameter('option_value_id', $optionAndOptionValueId->optionValueId())
                ->executeQuery();
            $adminSelectEntryExists = $this->connection->createQueryBuilder()
                    ->select('COUNT(*)')
                    ->from('products_properties_admin_select')
                    ->where('products_id = :products_id')
                    ->setParameter('products_id', $productVariant->productId())
                    ->andWhere('properties_id = :properties_id')
                    ->setParameter('properties_id', $optionAndOptionValueId->optionId())
                    ->andWhere('properties_values_id = :properties_values_id')
                    ->setParameter('properties_values_id',
                        $optionAndOptionValueId->optionValueId())
                    ->executeQuery()
                    ->fetchNumeric()[0] === '1';
            if ($adminSelectEntryExists === false) {

                $insertAdminSelectQuery .= '(?, ?, ?), ';
                $insertAdminSelectData[] = $productVariant->productId();
                $insertAdminSelectData[] = $optionAndOptionValueId->optionId();
                $insertAdminSelectData[] = $optionAndOptionValueId->optionValueId();
            }
        }

        if (empty($insertAdminSelectData) === false) {

            $insertAdminSelectQuery = substr($insertAdminSelectQuery, 0, -2);
            $this->connection->prepare($insertAdminSelectQuery)->executeQuery($insertAdminSelectData);
        }
    }


    /**
     * @param ProductVariant $productVariant
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateProductVariantImageList(ProductVariant $productVariant): void
    {
        $this->connection->createQueryBuilder()
            ->delete('product_image_list_combi')
            ->where('products_properties_combis_id = :variant_id')
            ->setParameter('variant_id', $productVariant->id())
            ->executeQuery();
        if ($productVariant->imageListId() !== null) {
            $this->connection->createQueryBuilder()
                ->insert('product_image_list_combi')
                ->setValue('products_properties_combis_id', ':variant_id')
                ->setValue('product_image_list_id', ':image_list_id')
                ->setParameter('variant_id', $productVariant->id())
                ->setParameter('image_list_id', $productVariant->imageListId())
                ->executeQuery();
        }
    }


    /**
     * @description all necessary information about each option and optionValue must be
     *              inserted into the table `products_properties_index`.
     *              The information is already available in different tables related to properties.
     *              It must be copied for the current version of GambioAdmin to be able to see & edit the combis
     *
     * @param int[] $variantIds
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function indexCombiAndOptions(int ...$variantIds): void
    {
        if (count($variantIds) === 0) {
            return;
        }

        $this->connection->createQueryBuilder()
            ->delete('products_properties_index')
            ->where('products_properties_combis_id IN (' . implode(', ', $variantIds) . ')')
            ->executeQuery();
        $query = "
            INSERT INTO `products_properties_index`
            (
                `products_id`,
                `language_id`,
                `properties_id`,
                `products_properties_combis_id`,
                `properties_values_id`,
                `properties_name`,
                `properties_admin_name`,
                `properties_sort_order`,
                `values_name`,
                `values_price`,
                `value_sort_order`
            )
            SELECT
                `ppc`.`products_id`,
                `pd`.`language_id`,
                `p`.`properties_id`,
                `ppc`.`products_properties_combis_id`,
                `pv`.`properties_values_id`,
                `pd`.`properties_name`,
                `pd`.`properties_admin_name`,
                `p`.`sort_order` as 'properties_sort_order',
                `pvd`.`values_name`,
                `pv`.`value_price`,
                `pv`.`sort_order` as 'properties_values_sort_order'
            FROM `properties_values` `pv`
            LEFT JOIN `properties` `p` ON `p`.`properties_id`=`pv`.`properties_id`
            LEFT JOIN `properties_description` as `pd` ON `p`.`properties_id`=`pd`.`properties_id`
            LEFT JOIN `properties_values_description` as `pvd` ON `pvd`.`properties_values_id`=`pv`.`properties_values_id` AND `pvd`.`language_id`=`pd`.`language_id`
            LEFT JOIN `products_properties_combis_values` as `ppcv` ON `ppcv`.`properties_values_id`=`pv`.`properties_values_id`
            LEFT JOIN `products_properties_combis` `ppc` ON `ppc`.`products_properties_combis_id`=`ppcv`.`products_properties_combis_id`
            WHERE `ppcv`.`products_properties_combis_id` IN (" . implode(',', $variantIds) . ")
        ";

        $stmt = $this->connection->prepare($query);
        $stmt->executeQuery();
    }
}