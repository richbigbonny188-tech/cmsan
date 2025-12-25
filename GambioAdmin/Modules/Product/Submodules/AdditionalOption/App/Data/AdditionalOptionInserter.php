<?php
/*--------------------------------------------------------------------
 ProductOptionInserter.php 2023-06-09
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
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionAlreadyExistsException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\InsertionOfAdditionalOptionsFailedException;

/**
 * Class AdditionalOptionInserter
 *
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data
 */
class AdditionalOptionInserter
{
    use AdditionalOptionFloatConverter;
    
    /**
     * AdditionalOptionInserter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }
    
    
    /**
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param AdditionalOptionStock    $additionalOptionStock
     * @param int                      $sortOrder
     *
     * @return int
     *
     * @throws InsertionOfAdditionalOptionsFailedException
     * @throws AdditionalOptionAlreadyExistsException
     */
    public function createAdditionalOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
        int                      $sortOrder = 0
    ): int {
        try {
            $this->connection->beginTransaction();

            if ($this->additionalOptionExists($productId, $optionAndOptionValueId)) {
                throw AdditionalOptionAlreadyExistsException::forProductIdAndOptionAndOptionValueId($productId,
                                                                                                    $optionAndOptionValueId);
            }
            
            $additionalOptionId = $this->insertAdditionalOption($productId,
                                                                $optionAndOptionValueId,
                                                                $optionValueCustomization,
                                                                $additionalOptionStock,
                                                                $sortOrder);
            
            $this->insertImageListId($additionalOptionId, $imageListId);
            
            $this->connection->commit();
            
            return $additionalOptionId;
        } catch (AdditionalOptionAlreadyExistsException $alreadyExistsException) {
            throw $alreadyExistsException;
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw InsertionOfAdditionalOptionsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param OptionValueCustomization $optionValueCustomization
     * @param AdditionalOptionStock    $additionalOptionStock
     * @param int                      $sortOrder
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertAdditionalOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
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
            ->setParameter('attributes_stock', $additionalOptionStock->stock())
            ->setParameter('stock_type', $additionalOptionStock->stockType())
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
     * @throws InsertionOfAdditionalOptionsFailedException
     * @throws AdditionalOptionAlreadyExistsException
     */
    public function createMultipleAdditionalOptions(array ...$creationArguments): array
    {
        $result = [];
        
        foreach ($creationArguments as $creationArgument) {
            $result[] = $this->createAdditionalOption(...$creationArgument);
        }
        
        return $result;
    }
    
    
    /**
     * @param int         $additionalOptionId
     * @param ImageListId $imageListId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertImageListId(int $additionalOptionId, ImageListId $imageListId)
    {
        if ($imageListId->value() !== null) {
            $this->connection->createQueryBuilder()
                ->insert('product_image_list_attribute')
                ->setValue('products_attributes_id', ':products_attributes_id')
                ->setValue('product_image_list_id', ':product_image_list_id')
                ->setParameter('products_attributes_id', $additionalOptionId)
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
    private function additionalOptionExists(
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