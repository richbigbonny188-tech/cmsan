<?php
/*--------------------------------------------------------------
   ProductVariantsInserter.php 2023-06-27
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
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;

/**
 * Class ProductVariantsInserter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Data
 */
class ProductVariantsInserter
{
    use WeightAndPriceTypeValidator;

    /**
     * ProductVariantsInserter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection){}


    /**
     * @param ProductId $productId
     * @param OptionAndOptionValueIds $combination
     * @param ImageListId $imageListId
     * @param ProductCustomization $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock $stock
     * @param int $sortOrder
     *
     * @return int
     *
     * @throws InsertionOfProductVariantsFailed
     * @throws \Doctrine\DBAL\Exception
     */
    public function createProductVariant(
        ProductId                    $productId,
        OptionAndOptionValueIds      $combination,
        ImageListId                  $imageListId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
    ): int
    {
        try {
            $this->connection->beginTransaction();

            $variantId = $this->insertProductVariant($productId,
                $productCustomization,
                $productIdentificationNumbers,
                $stock,
                $sortOrder);
            $this->connectVariantAndOptionValueIds($productId, $variantId, $combination);
            $this->connectVariantAndImageList($variantId, $imageListId);
            $this->indexCombiAndOptions($variantId);

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw InsertionOfProductVariantsFailed::becauseOfException($exception);
        }

        return $variantId;
    }

    /**
     * @param ProductId $productId
     * @param ProductCustomization $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock $stock
     * @param int $sortOrder
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertProductVariant(
        ProductId                    $productId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
    ): int
    {
        $this->connection->createQueryBuilder()
            ->insert('products_properties_combis')
            ->setValue('products_id', ':products_id')
            ->setValue('combi_quantity', ':combi_quantity')
            ->setValue('stock_type', ':stock_type')
            ->setValue('combi_shipping_status_id', ':combi_shipping_status_id')
            ->setValue('combi_weight', ':combi_weight')
            ->setValue('combi_weight_type', ':combi_weight_type')
            ->setValue('combi_price_type', ':combi_price_type')
            ->setValue('products_vpe_id', ':products_vpe_id')
            ->setValue('vpe_value', ':vpe_value')
            ->setValue('combi_model', ':combi_model')
            ->setValue('combi_ean', ':combi_ean')
            ->setValue('combi_price', ':combi_price')
            ->setValue('gtin', ':gtin')
            ->setValue('asin', ':asin')
            ->setValue('sort_order', ':sort_order')
            ->setParameter('products_id', $productId->value())
            ->setParameter('combi_shipping_status_id', $productCustomization->deliveryTimeId())
            ->setParameter('combi_weight', $productCustomization->weight())
            ->setParameter('combi_weight_type', $this->getDatabaseTypeFieldValue($productCustomization->weightType()))
            ->setParameter('combi_price_type', $this->getDatabaseTypeFieldValue($productCustomization->priceType()))
            ->setParameter('combi_price', $productCustomization->price())
            ->setParameter('products_vpe_id', $productCustomization->vpeUnitId() ?? 0)
            ->setParameter('vpe_value', $productCustomization->vpeScalarValue())
            ->setParameter('combi_model', $productIdentificationNumbers->modelNumber())
            ->setParameter('combi_ean', $productIdentificationNumbers->ean())
            ->setParameter('gtin', $productIdentificationNumbers->gtin())
            ->setParameter('asin', $productIdentificationNumbers->asin())
            ->setParameter('combi_quantity', $stock->stock())
            ->setParameter('stock_type', $stock->stockType())
            ->setParameter('sort_order', $sortOrder)
            ->executeQuery();
        return (int)$this->connection->lastInsertId();
    }

    /**
     * @param ProductId $productId
     * @param int $variantId
     * @param OptionAndOptionValueIds $combination
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function connectVariantAndOptionValueIds(
        ProductId               $productId,
        int                     $variantId,
        OptionAndOptionValueIds $combination
    ): void
    {
        $this->connection->createQueryBuilder()
            ->delete('products_properties_combis_values')
            ->where('products_properties_combis_id = :variant_id')
            ->setParameter('variant_id', $variantId)
            ->executeQuery();

        $insertAdminSelectData = [];
        $insertAdminSelectQuery = '
                    INSERT INTO `products_properties_admin_select`
                    (`products_id`, `properties_id`, `properties_values_id`) VALUES
                    ';
        foreach ($combination as $optionAndOptionValueId) {
            $subQuery = '(SELECT `properties_values_id` FROM `properties_values` '
                . 'WHERE `properties_values_id` = :option_value_id AND  `properties_id` = :option_id)';

            $this->connection->createQueryBuilder()
                ->insert('products_properties_combis_values')
                ->setValue('products_properties_combis_id', ':variant_id')
                ->setValue('properties_values_id', $subQuery)
                ->setValue('options_id', ':option_id')
                ->setParameter('variant_id', $variantId)
                ->setParameter('option_id', $optionAndOptionValueId->optionId())
                ->setParameter('option_value_id', $optionAndOptionValueId->optionValueId())
                ->executeQuery();
            $adminSelectEntryExists = $this->connection->createQueryBuilder()
                    ->select('COUNT(*)')
                    ->from('products_properties_admin_select')
                    ->where('products_id = :products_id')
                    ->setParameter('products_id', $productId->value())
                    ->andWhere('properties_id = :properties_id')
                    ->setParameter('properties_id', $optionAndOptionValueId->optionId())
                    ->andWhere('properties_values_id = :properties_values_id')
                    ->setParameter('properties_values_id', $optionAndOptionValueId->optionValueId())
                    ->executeQuery()
                    ->fetchNumeric()[0] === '1';
            if ($adminSelectEntryExists === false) {

                $insertAdminSelectQuery .= '(?, ?, ?), ';
                $insertAdminSelectData[] = $productId->value();
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
     * @param int $variantId
     * @param ImageListId $imageListId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function connectVariantAndImageList(int $variantId, ImageListId $imageListId): void
    {
        if ($imageListId->value() !== null) {
            $this->connection->createQueryBuilder()
                ->insert('product_image_list_combi')
                ->setValue('products_properties_combis_id', ':variant_id')
                ->setValue('product_image_list_id', ':image_list_id')
                ->setParameter('variant_id', $variantId)
                ->setParameter('image_list_id', $imageListId->value())
                ->executeQuery();
        }
    }

    /**
     * @description all necessary information about each option and optionValue must be
     *              inserted into the table `products_properties_index`.
     *              The information is already available in different tables related to properties.
     *              It must be copied for the current version of GambioAdmin to be able to see & edit the combis
     *
     * @param int $variantId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function indexCombiAndOptions(int $variantId): void
    {
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
            WHERE `ppcv`.`products_properties_combis_id` = :variant_id
        ";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':variant_id', $variantId);
        $stmt->executeQuery();
    }

    /**
     * @param array ...$creationArgs
     *
     * @return int[]
     *
     * @throws InsertionOfProductVariantsFailed
     */
    public function createMultipleProductVariants(array ...$creationArgs): array
    {
        try {
            $this->connection->beginTransaction();

            $variantIds = [];
            foreach ($creationArgs as $args) {
                [
                    $productId,
                    $combination,
                    $imageListId,
                    $productCustomization,
                    $productIdentificationNumbers,
                    $stock,
                    $sortOrder
                ] = $args;

                $variantId = $this->insertProductVariant($productId,
                    $productCustomization,
                    $productIdentificationNumbers,
                    $stock,
                    $sortOrder);
                $this->connectVariantAndOptionValueIds($productId, $variantId, $combination);
                $this->connectVariantAndImageList($variantId, $imageListId);
                $this->indexCombiAndOptions($variantId);

                $variantIds[] = $variantId;
            }

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw InsertionOfProductVariantsFailed::becauseOfException($exception);
        }

        return $variantIds;
    }
}