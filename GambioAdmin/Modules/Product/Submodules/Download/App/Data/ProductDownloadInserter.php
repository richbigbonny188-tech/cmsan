<?php
/*--------------------------------------------------------------------
 ProductDownloadInserter.php 2023-06-26
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
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\Traits\ProductDownloadFloatConverter;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\InsertionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadAlreadyExistsException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;

/**
 * Class ProductDownloadInserter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Data
 */
class ProductDownloadInserter
{
    use ProductDownloadFloatConverter;

    /**
     * ProductDownloadInserter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }
    
    
    /**
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param ProductDownloadStock     $productDownloadStock
     * @param int                      $sortOrder
     *
     * @return int
     *
     * @throws InsertionOfProductDownloadsFailedException
     * @throws ProductDownloadAlreadyExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createProductDownload(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productDownloadStock,
        int                      $sortOrder = 0
    ): int {
        try {
            $this->connection->beginTransaction();

            if ($this->productOptionExists($productId, $optionAndOptionValueId)) {
                throw ProductDownloadAlreadyExistsException::forProductIdAndOptionAndOptionValueId($productId,
                                                                                                   $optionAndOptionValueId);
            }
            
            $productOptionId = $this->insertProductOption($productId,
                                                          $optionAndOptionValueId,
                                                          $optionValueCustomization,
                                                          $productDownloadStock,
                                                          $sortOrder);
            
            $this->insertImageListId($productOptionId, $imageListId);
            
            $this->connection->commit();
            
            return $productOptionId;
        } catch (ProductDownloadAlreadyExistsException $alreadyExistsException) {
            throw $alreadyExistsException;
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw InsertionOfProductDownloadsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param OptionValueCustomization $optionValueCustomization
     * @param ProductDownloadStock     $productDownloadStock
     * @param int                      $sortOrder
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertProductOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productDownloadStock,
        int                      $sortOrder = 0
    ): int {
        [
            'prefix' => $pricePrefix,
            'value'  => $priceValue,
        ] = $this->convertFloatToPositiveFloatAndPrefix($optionValueCustomization->price());

        [
            'prefix' => $weightPrefix,
            'value'  => $weightValue,
        ] = $this->convertFloatToPositiveFloatAndPrefix($optionValueCustomization->weight());

        $this->connection->createQueryBuilder()
            ->insert('products_attributes')
            ->setValue('products_id', ':products_id')
            ->setValue('options_id',
                       '(SELECT DISTINCT `products_options_id` FROM `products_options` WHERE `options_id` = '
                       . $optionAndOptionValueId->optionId() . ')')
            ->setValue('options_values_id',
                       '(SELECT DISTINCT `products_options_values_id` FROM `products_options_values` WHERE `option_value_id` = '
                       . $optionAndOptionValueId->optionValueId() . ')')
            ->setValue('options_values_price', ':options_values_price')
            ->setValue('price_prefix', ':price_prefix')
            ->setValue('attributes_model', ':attributes_model')
            ->setValue('attributes_stock', ':attributes_stock')
            ->setValue('stock_type', ':stock_type')
            ->setValue('options_values_weight', ':options_values_weight')
            ->setValue('weight_prefix', ':weight_prefix')
            ->setValue('sortorder', ':sortorder')
            ->setParameter('products_id', $productId->value())
            ->setParameter('options_values_price', $priceValue)
            ->setParameter('price_prefix', $pricePrefix)
            ->setParameter('attributes_model', $optionValueCustomization->modelNumber())
            ->setParameter('attributes_stock', $productDownloadStock->stock())
            ->setParameter('stock_type', $productDownloadStock->stockType())
            ->setParameter('options_values_weight', $optionValueCustomization->weight())
            ->setParameter('options_values_weight', $weightValue)
            ->setParameter('weight_prefix', $weightPrefix)
            ->setParameter('sortorder', $sortOrder)
            ->executeQuery();
        
        return (int)$this->connection->lastInsertId();
    }
    
    
    /**
     * @param array $creationArguments
     *
     * @return int[]
     *
     * @throws InsertionOfProductDownloadsFailedException
     * @throws ProductDownloadAlreadyExistsException
     */
    public function createMultipleProductDownloads(array ...$creationArguments): array
    {
        $result = [];
        
        foreach ($creationArguments as $creationArgument) {
            $result[] = $this->createProductDownload(...$creationArgument);
        }
        
        return $result;
    }
    
    
    /**
     * @param int         $productOptionId
     * @param ImageListId $imageListId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertImageListId(int $productOptionId, ImageListId $imageListId)
    {
        if ($imageListId->value() !== null) {
            $this->connection->createQueryBuilder()
                ->insert('product_image_list_attribute')
                ->setValue('products_attributes_id', ':products_attributes_id')
                ->setValue('product_image_list_id', ':product_image_list_id')
                ->setParameter('products_attributes_id', $productOptionId)
                ->setParameter('product_image_list_id', $imageListId->value())
                ->executeQuery();
        }
    }
    
    
    /**
     * @param ProductId              $productId
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    private function productOptionExists(
        ProductId              $productId,
        OptionAndOptionValueId $optionAndOptionValueId
    ): bool {
        return $this->connection->createQueryBuilder()
                   ->select('*')
                   ->from('products_attributes', 'pa')
                   ->leftJoin('pa',
                              'products_options_values',
                              'pov',
                              'pov.products_options_values_id=pa.options_values_id')
                   ->leftJoin('pa',
                              'product_image_list_attribute',
                              'pila',
                              'pa.products_attributes_id=pila.products_attributes_id')
                   ->leftJoin('pa', 'products_options', 'po', 'po.products_options_id=pa.options_id')
                   ->where('pa.products_id=:products_id')
                   ->andWhere('po.options_id=:options_id')
                   ->andWhere('pov.option_value_id=:option_value_id')
                   ->setParameter('products_id', $productId->value())
                   ->setParameter('options_id', $optionAndOptionValueId->optionId())
                   ->setParameter('option_value_id', $optionAndOptionValueId->optionValueId())
                   ->executeQuery()
                   ->rowCount() !== 0;
    }
}