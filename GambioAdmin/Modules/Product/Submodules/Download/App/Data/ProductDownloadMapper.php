<?php
/*--------------------------------------------------------------------
 ProductOptionMapper.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Data;

use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\Traits\ProductDownloadFloatConverter;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFactory;

/**
 * Class ProductOptionMapper
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Data
 */
class ProductDownloadMapper
{
    use ProductDownloadFloatConverter;
    
    /**
     * ProductOptionMapper constructor.
     *
     * @param ProductDownloadFactory $factory
     */
    public function __construct(private ProductDownloadFactory $factory) { }
    
    
    /**
     * @param array $productDownloads
     *
     * @return ProductDownloads
     */
    public function mapProductDownloads(array $productDownloads): ProductDownloads
    {
        $options = array_map([$this, 'mapProductDownload'], $productDownloads);
        
        return ProductDownloads::create(...$options);
    }
    
    
    /**
     * @param array $data
     *
     * @return ProductDownload
     */
    public function mapProductDownload(array $data): ProductDownload
    {
        $productOptionId        = $this->mapProductOptionId((int)$data['products_attributes_id']);
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
        $stockType              = empty($data['stock_type']) ? ProductDownloadStock::STOCK_TYPE_NOT_MANAGED : $data['stock_type'];
        $stock                  = $this->mapProductDownloadStock((float)$data['attributes_stock'], $stockType);
        
        return ProductDownload::create($productOptionId,
                                     $productId,
                                     $optionAndOptionValueId,
                                     $imageListId,
                                     $customization,
                                     $stock,
                                     (int)$data['sortorder']);
    }
    
    
    /**
     * @param AdditionalOptionId       $productOptionId
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param ProductDownloadStock     $productOptionStock
     * @param int                      $sortOrder
     *
     * @return ProductDownload
     */
    protected function createProductDownload(
        additionalOptionId       $productOptionId,
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productOptionStock,
        int                      $sortOrder
    ): ProductDownload {
        return ProductDownload::create($productOptionId,
                                       $productId,
                                       $optionAndOptionValueId,
                                       $imageListId,
                                       $optionValueCustomization,
                                       $productOptionStock,
                                       $sortOrder);
    }
    
    
    /**
     * @param int $productOptionId
     *
     * @return AdditionalOptionId
     */
    public function mapProductOptionId(int $productOptionId): AdditionalOptionId
    {
        return $this->factory->createProductOptionId($productOptionId);
    }
    
    
    /**
     * @param array $productOptionIds
     *
     * @return AdditionalOptionIds
     */
    public function mapProductOptionIds(array $productOptionIds): AdditionalOptionIds
    {
        return AdditionalOptionIds::create(...array_map([$this, 'mapProductOptionId'], $productOptionIds));
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
        float $weight,
        float $price
    ): OptionValueCustomization {
        
        return $this->factory->createOptionValueCustomization($modelNumber, $weight, $price);
    }
    
    
    /**
     * @param float    $stock
     * @param string $stockType
     *
     * @return ProductDownloadStock
     */
    public function mapProductDownloadStock(float $stock, string $stockType): ProductDownloadStock
    {
        return $this->factory->createProductDownloadStock($stock, $stockType);
    }
}