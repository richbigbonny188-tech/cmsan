<?php
/*--------------------------------------------------------------------
 ProductOptionUpdater.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Traits\AdditionalOptionFloatConverter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\StorageOfAdditionalOptionsFailedException;

/**
 * Class AdditionalOptionUpdater
 *
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data
 */
class AdditionalOptionUpdater
{
    use AdditionalOptionFloatConverter;
    
    /**
     * AdditionalOptionUpdater constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }

    

    /**
     * @param AdditionalOption ...$productOptions
     *
     * @throws StorageOfAdditionalOptionsFailedException
     */
    public function storeAdditionalOptions(AdditionalOption ...$productOptions): void
    {
        try {
            $this->connection->beginTransaction();
            
            array_map([$this, 'updateAdditionalOption'], $productOptions);

            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw StorageOfAdditionalOptionsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param AdditionalOption $productOption
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateAdditionalOption(AdditionalOption $productOption): void
    {
        [
            'prefix' => $pricePrefix,
            'value'  => $priceValue,
        ] = $this->convertFloatToPositiveFloatAndPrefix($productOption->price());

        [
            'prefix' => $weightPrefix,
            'value'  => $weightValue,
        ] = $this->convertFloatToPositiveFloatAndPrefix($productOption->weight());
        
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
            ->setParameter('attributes_model', $productOption->modelNumber())
            ->setParameter('attributes_stock', $productOption->stock())
            ->setParameter('stock_type', $productOption->stockType())
            ->setParameter('options_values_weight', $weightValue)
            ->setParameter('weight_prefix', $weightPrefix)
            ->setParameter('sortorder', $productOption->sortOrder())
            ->setParameter('products_attributes_id', $productOption->id())
            ->executeQuery();
        
        $this->updateAdditionalOptionImageList($productOption);
    }
    
    
    /**
     * @param AdditionalOption $productOption
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateAdditionalOptionImageList(AdditionalOption $productOption): void
    {
        $this->connection->createQueryBuilder()
            ->delete('product_image_list_attribute')
            ->where('products_attributes_id = :products_attributes_id')
            ->setParameter('products_attributes_id', $productOption->id())
            ->executeQuery();
        
        if ($productOption->imageListId() !== null) {
            $this->connection->createQueryBuilder()
                ->insert('product_image_list_attribute')
                ->setValue('products_attributes_id', ':products_attributes_id')
                ->setValue('product_image_list_id', ':product_image_list_id')
                ->setParameter('products_attributes_id', $productOption->id())
                ->setParameter('product_image_list_id', $productOption->imageListId())
                ->executeQuery();
        }
    }
}