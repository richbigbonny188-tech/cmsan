<?php
/*--------------------------------------------------------------
   ProductVariantFactory.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Services;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;

/**
 * Class ProductVariantFactory
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Services
 */
class ProductVariantFactory
{
    /**
     * @param int $productId
     *
     * @return ProductId
     */
    public function createProductId(int $productId): ProductId
    {
        return ProductId::create($productId);
    }


    /**
     * @param int $variantId
     *
     * @return ProductVariantId
     */
    public function createProductVariantId(int $variantId): ProductVariantId
    {
        return ProductVariantId::create($variantId);
    }


    /**
     * @param ProductVariantId ...$variantIds
     *
     * @return ProductVariantIds
     */
    public function createProductsVariantIds(ProductVariantId ...$variantIds): ProductVariantIds
    {
        return ProductVariantIds::create(...$variantIds);
    }


    /**
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return OptionAndOptionValueId
     */
    public function createOptionAndOptionValueId(int $optionId, int $optionValueId): OptionAndOptionValueId
    {
        return OptionAndOptionValueId::create($optionId, $optionValueId);
    }


    /**
     * @param OptionAndOptionValueId ...$optionAndOptionValueIds
     *
     * @return OptionAndOptionValueIds
     */
    public function createOptionAndOptionValueIds(OptionAndOptionValueId ...$optionAndOptionValueIds
    ): OptionAndOptionValueIds
    {
        return OptionAndOptionValueIds::create(...$optionAndOptionValueIds);
    }


    /**
     * @param int|null $id
     *
     * @return ImageListId
     */
    public function createImageListId(?int $id = null): ImageListId
    {
        return $id === null ? ImageListId::createAsNonExistent() : ImageListId::createAsExisting($id);
    }


    /**
     * @param int $deliveryTimeId
     * @param string $priceType
     * @param float $price
     * @param string $weightType
     * @param float $weight
     * @param float $vpeScalarValue
     * @param int|null $vpeUnitId
     *
     * @return ProductCustomization
     */
    public function createProductCustomization(
        int    $deliveryTimeId,
        string $priceType = ProductCustomization::PRICE_TYPE_ADDITION,
        float  $price = 0,
        string $weightType = ProductCustomization::WEIGHT_TYPE_ADDITION,
        float  $weight = 0,
        float  $vpeScalarValue = 0,
        ?int   $vpeUnitId = null
    ): ProductCustomization
    {
        return ProductCustomization::create($priceType,
            $price,
            $weightType,
            $weight,
            $vpeScalarValue,
            $deliveryTimeId,
            $vpeUnitId);
    }


    /**
     * @param string $modelNumber
     * @param string $ean
     * @param string $gtin
     * @param string $asin
     *
     * @return ProductIdentificationNumbers
     */
    public function createProductIdentificationNumbers(
        string $modelNumber = '',
        string $ean = '',
        string $gtin = '',
        string $asin = ''
    ): ProductIdentificationNumbers
    {
        return ProductIdentificationNumbers::create($modelNumber, $ean, $gtin, $asin);
    }


    /**
     * @param float $stock
     * @param string $stockType
     *
     * @return ProductVariantStock
     */
    public function createProductVariantStock(
        float  $stock = 0,
        string $stockType = ProductVariantStock::STOCK_TYPE_NOT_MANAGED
    ): ProductVariantStock
    {
        return ProductVariantStock::create($stock, $stockType);
    }


    /**
     * @param ProductVariantId $variantId
     * @param ProductId $productId
     * @param OptionAndOptionValueIds $combination
     * @param ImageListId $imageListId
     * @param ProductCustomization $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock $stock
     * @param int $sortOrder
     *
     * @return ProductVariant
     */
    public function createProductVariant(
        ProductVariantId             $variantId,
        ProductId                    $productId,
        OptionAndOptionValueIds      $combination,
        ImageListId                  $imageListId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
    ): ProductVariant
    {
        return ProductVariant::create($variantId,
            $productId,
            $combination,
            $imageListId,
            $productCustomization,
            $productIdentificationNumbers,
            $stock,
            $sortOrder);
    }


    /**
     * @param ProductVariant ...$productVariants
     *
     * @return ProductVariants
     */
    public function createProductVariants(ProductVariant ...$productVariants): ProductVariants
    {
        return ProductVariants::create(...$productVariants);
    }
}