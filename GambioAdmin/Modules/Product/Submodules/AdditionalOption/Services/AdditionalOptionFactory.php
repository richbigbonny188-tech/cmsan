<?php
/*--------------------------------------------------------------------
 AdditionalOptionFactory.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;

/**
 * Class AdditionalOptionFactory
 *
 * @package  Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services
 * @internal No method for creation Aggregate root / collection
 */
class AdditionalOptionFactory
{
    /**
     * @param int $additionalOptionId
     *
     * @return AdditionalOptionId
     */
    public function createAdditionalOptionId(int $additionalOptionId): AdditionalOptionId
    {
        return AdditionalOptionId::create($additionalOptionId);
    }
    
    
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
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return OptionAndOptionValueId
     */
    public function createOptionAndOptionValueId(
        int $optionId,
        int $optionValueId
    ): OptionAndOptionValueId {
        return OptionAndOptionValueId::create($optionId, $optionValueId);
    }
    
    
    /**
     * @param int|null $imageListId
     *
     * @return ImageListId
     */
    public function createImageListId(?int $imageListId): ImageListId
    {
        return $imageListId === null ? ImageListId::createAsNonExistent() : ImageListId::createAsExisting($imageListId);
    }
    
    
    /**
     * @param string $modelNumber
     * @param float  $weight
     * @param float  $price
     *
     * @return OptionValueCustomization
     */
    public function createOptionValueCustomization(
        string $modelNumber,
        float  $weight,
        float  $price
    ): OptionValueCustomization {
        return OptionValueCustomization::create($modelNumber, $weight, $price);
    }
    
    
    /**
     * @param float  $stock
     * @param string $stockType
     *
     * @return AdditionalOptionStock
     */
    public function createAdditionalOptionStock(
        float  $stock = 0,
        string $stockType = AdditionalOptionStock::STOCK_TYPE_NOT_MANAGED
    ): AdditionalOptionStock {
        return AdditionalOptionStock::create($stock, $stockType);
    }
}