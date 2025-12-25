<?php
/*--------------------------------------------------------------------
 ProductOptionUpdater.php 2023-06-26
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
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\StorageOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;

/**
 * Class ProductOptionUpdater
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Data
 */
class ProductDownloadUpdater
{
    use ProductDownloadFloatConverter;

    /**
     * ProductOptionUpdater constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }
    

    /**
     * @param ProductDownload ...$productDownloads
     *
     * @throws StorageOfProductDownloadsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void
    {
        try {
            $this->connection->beginTransaction();
            
            array_map([$this, 'updateProductDownload'], $productDownloads);

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw StorageOfProductDownloadsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ProductDownload $productDownload
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateProductDownload(ProductDownload $productDownload): void
    {
        [
            'prefix' => $pricePrefix,
            'value'  => $priceValue,
        ] = $this->convertFloatToPositiveFloatAndPrefix($productDownload->price());

        [
            'prefix' => $weightPrefix,
            'value'  => $weightValue,
        ] = $this->convertFloatToPositiveFloatAndPrefix($productDownload->weight());
        
        $this->connection->createQueryBuilder()
            ->update('products_attributes')
            ->set('options_values_price', ':options_values_price')
            ->set('price_prefix', ':price_prefix')
            ->set('attributes_model', ':attributes_model')
            ->set('attributes_stock', ':attributes_stock')
            ->set('stock_type', ':stock_type')
            ->set('options_values_weight', ':options_values_weight')
            ->set('weight_prefix', ':weight_prefix')
            ->set('sortorder', ':sortorder')
            ->where('products_attributes_id = :products_attributes_id')
            ->setParameter('options_values_price', $priceValue)
            ->setParameter('price_prefix', $pricePrefix)
            ->setParameter('attributes_model', $productDownload->modelNumber())
            ->setParameter('attributes_stock', $productDownload->stock())
            ->setParameter('stock_type', $productDownload->stockType())
            ->setParameter('options_values_weight', $weightValue)
            ->setParameter('weight_prefix', $weightPrefix)
            ->setParameter('sortorder', $productDownload->sortOrder())
            ->setParameter('products_attributes_id', $productDownload->id())
            ->executeQuery();
        
        $this->updateProductOptionImageList($productDownload);
    }
    
    
    /**
     * @param ProductDownload $productDownload
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateProductOptionImageList(ProductDownload $productDownload): void
    {
        $this->connection->createQueryBuilder()
            ->delete('product_image_list_attribute')
            ->where('products_attributes_id = :products_attributes_id')
            ->setParameter('products_attributes_id', $productDownload->id())
            ->executeQuery();
        
        if ($productDownload->imageListId() !== null) {
            $this->connection->createQueryBuilder()
                ->insert('product_image_list_attribute')
                ->setValue('products_attributes_id', ':products_attributes_id')
                ->setValue('product_image_list_id', ':product_image_list_id')
                ->setParameter('products_attributes_id', $productDownload->id())
                ->setParameter('product_image_list_id', $productDownload->imageListId())
                ->executeQuery();
        }
    }
}