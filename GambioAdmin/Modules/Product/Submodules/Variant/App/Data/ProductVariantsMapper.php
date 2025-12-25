<?php
/*--------------------------------------------------------------
   ProductVariantsMapper.php 2024-03-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\Data;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;

/**
 * Class ProductVariantsMapper
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Data
 */
class ProductVariantsMapper
{
    /**
     * ProductVariantsMapper constructor.
     *
     * @param ProductVariantFactory $factory
     */
    public function __construct(private ProductVariantFactory $factory)
    {
    }


    /**
     * @param array $productVariants
     *
     * @return ProductVariants
     */
    public function mapProductVariants(array $productVariants): ProductVariants
    {
        $variants = array_map([$this, 'mapProductVariant'], $productVariants);

        return $this->factory->createProductVariants(...$variants);
    }


    /**
     * @param array $data
     *
     * @return ProductVariant
     * @todo alter table products_properties_combis.products_vpe_id nullable
     */
    public function mapProductVariant(array $data): ProductVariant
    {
        $variantId = $this->factory->createProductVariantId((int)$data['products_properties_combis_id']);
        $productId = $this->factory->createProductId((int)$data['products_id']);
        $combinations = $this->mapCombinationsFromString((string)$data['combination']);
        $imageListId = $data['product_image_list_id'] === null ? null : (int)$data['product_image_list_id'];
        $imageListId = $this->factory->createImageListId($imageListId);
        $priceType = $data['combi_price_type']
        === 'calc' ? ProductCustomization::PRICE_TYPE_ADDITION : ProductCustomization::PRICE_TYPE_REPLACING;
        $weightType = $data['combi_weight_type']
        === 'calc' ? ProductCustomization::WEIGHT_TYPE_ADDITION : ProductCustomization::WEIGHT_TYPE_REPLACING;
        $vpeId = (int)$data['products_vpe_id'] === 0 ? null : (int)$data['products_vpe_id'];

        $productCustomization = $this->factory->createProductCustomization((int)$data['combi_shipping_status_id'],
            $priceType,
            (float)$data['combi_price'],
            $weightType,
            (float)$data['combi_weight'],
            (float)$data['vpe_value'],
            $vpeId);

        $productIdentificationNumbers = $this->factory->createProductIdentificationNumbers($data['combi_model'],
            $data['combi_ean'],
            $data['gtin'] ?? '',
            $data['asin'] ?? '');

        $stockType = $data['stock_type'] !== null
        && $data['stock_type'] !== '' ? $data['stock_type'] : ProductVariantStock::STOCK_TYPE_NOT_MANAGED;
        $stock = $this->factory->createProductVariantStock((float)$data['combi_quantity'], $stockType);

        return $this->factory->createProductVariant($variantId,
            $productId,
            $combinations,
            $imageListId,
            $productCustomization,
            $productIdentificationNumbers,
            $stock,
            (int)$data['sort_order']);
    }

    /**
     * @param string $combinationString
     *
     * @return OptionAndOptionValueIds
     */
    private function mapCombinationsFromString(string $combinationString): OptionAndOptionValueIds
    {
        $optionAndOptionValueIds = [];
        $pairs = explode('|', $combinationString);
        
        if (!$combinationString == '') {
            foreach ($pairs as $pair) {
                [$optionId, $optionValueId] = array_map('intval', explode('-', $pair));
                
                $optionAndOptionValueIds[] = $this->factory->createOptionAndOptionValueId($optionId, $optionValueId);
            }
        }

        return $this->factory->createOptionAndOptionValueIds(...$optionAndOptionValueIds);
    }

    /**
     * @param int $id
     *
     * @return ProductVariantId
     */
    public function mapProductVariantId(int $id): ProductVariantId
    {
        return $this->factory->createProductVariantId($id);
    }

    /**
     * @param int $id
     *
     * @return ProductId
     */
    public function mapProductId(int $id): ProductId
    {
        return $this->factory->createProductId($id);
    }

    /**
     * @param ProductVariantId ...$ids
     *
     * @return ProductVariantIds
     */
    public function mapProductVariantIds(ProductVariantId ...$ids): ProductVariantIds
    {
        return $this->factory->createProductsVariantIds(...$ids);
    }
}