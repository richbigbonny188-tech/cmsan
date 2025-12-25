<?php
/*--------------------------------------------------------------------
 AdditionalOptionMapper.php 2023-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Traits\AdditionalOptionFloatConverter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;

/**
 * Class AdditionalOptionMapper
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data
 */
class AdditionalOptionMapper
{
    use AdditionalOptionFloatConverter;
    
    /**
     * AdditionalOptionMapper constructor.
     *
     * @param AdditionalOptionFactory $factory
     */
    public function __construct(private AdditionalOptionFactory $factory) { }
    
    
    /**
     * @param array $additionalOptions
     *
     * @return AdditionalOptions
     */
    public function mapAdditionalOptions(array $additionalOptions): AdditionalOptions
    {
        $options = array_map([$this, 'mapAdditionalOption'], $additionalOptions);
        
        return AdditionalOptions::create(...$options);
    }
    
    
    /**
     * @param array $data
     *
     * @return AdditionalOption
     */
    public function mapAdditionalOption(array $data): AdditionalOption
    {
        $additionalOptionId     = $this->mapAdditionalOptionId((int)$data['products_attributes_id']);
        $productId              = $this->mapProductId((int)$data['products_id']);
        $optionAndOptionValueId = $this->mapOptionAndOptionValueId((int)$data['options_id'],
                                                                   (int)$data['option_value_id']);
        $imageListId            = $this->mapImageListId($data['product_image_list_id']
                                                        === null ? null : (int)$data['product_image_list_id']);
        $additionalWeight       = $this->convertPositiveFloatAndPrefixToFloat($data['weight_prefix'],
                                                                              (float)$data['options_values_weight']);
        $additionalPrice        = $this->convertPositiveFloatAndPrefixToFloat($data['price_prefix'],
                                                                              (float)$data['options_values_price']);
        $customization          = $this->mapOptionValueCustomization($data['attributes_model'],
                                                                     $additionalWeight,
                                                                     $additionalPrice);
        $stockType              = empty($data['stock_type']) ? AdditionalOptionStock::STOCK_TYPE_NOT_MANAGED : $data['stock_type'];
        $stock                  = $this->mapAdditionalOptionStock((float)$data['attributes_stock'], $stockType);
        
        return $this->createAdditionalOption($additionalOptionId,
                                        $productId,
                                        $optionAndOptionValueId,
                                        $imageListId,
                                        $customization,
                                        $stock,
                                        (int)$data['sortorder']);
    }
    
    
    /**
     * @param AdditionalOptionId       $id
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param AdditionalOptionStock    $additionalOptionStock
     * @param int                      $sortOrder
     *
     * @return AdditionalOption
     */
    protected function createAdditionalOption(
        AdditionalOptionId       $id,
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
        int                      $sortOrder
    ): AdditionalOption {
        return AdditionalOption::create($id,
                                        $productId,
                                        $optionAndOptionValueId,
                                        $imageListId,
                                        $optionValueCustomization,
                                        $additionalOptionStock,
                                        $sortOrder);
    }
    
    
    /**
     * @param int $additionalOptionId
     *
     * @return AdditionalOptionId
     */
    public function mapAdditionalOptionId(int $additionalOptionId): AdditionalOptionId
    {
        return $this->factory->createAdditionalOptionId($additionalOptionId);
    }
    
    
    /**
     * @param array $additionalOptionIds
     *
     * @return AdditionalOptionIds
     */
    public function mapAdditionalOptionIds(array $additionalOptionIds): AdditionalOptionIds
    {
        return AdditionalOptionIds::create(...array_map([$this, 'mapAdditionalOptionId'], $additionalOptionIds));
    }
    
    
    /**
     * @param int $productId
     *
     * @return ProductId
     */
    public function mapProductId(int $productId): ProductId
    {
        return $this->factory->createProductId($productId);
    }
    
    
    /**
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return OptionAndOptionValueId
     */
    public function mapOptionAndOptionValueId(int $optionId, int $optionValueId): OptionAndOptionValueId
    {
        return $this->factory->createOptionAndOptionValueId($optionId, $optionValueId);
    }
    
    
    /**
     * @param int|null $imageListId
     *
     * @return ImageListId
     */
    public function mapImageListId(?int $imageListId): ImageListId
    {
        return $this->factory->createImageListId($imageListId);
    }
    
    
    /**
     * @param string $modelNumber
     * @param float  $weight
     * @param float  $price
     *
     * @return OptionValueCustomization
     */
    public function mapOptionValueCustomization(
        string $modelNumber,
        float  $weight,
        float  $price
    ): OptionValueCustomization {
        return $this->factory->createOptionValueCustomization($modelNumber, $weight, $price);
    }
    
    
    /**
     * @param float  $stock
     * @param string $stockType
     *
     * @return AdditionalOptionStock
     */
    public function mapAdditionalOptionStock(float $stock, string $stockType): AdditionalOptionStock
    {
        return $this->factory->createAdditionalOptionStock($stock, $stockType);
    }
}