<?php
/*--------------------------------------------------------------
   DeprecatedEventRaisingProductVariant.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\ProductVariant\Model\Events as Deprecated;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class DeprecatedEventRaisingProductVariant
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model
 * @deprecated
 */
class DeprecatedEventRaisingProductVariant extends \Gambio\Admin\Modules\ProductVariant\Model\ProductVariant
{
    /**
     * @return ProductVariantId
     */
    private function createIdFromValue(): ProductVariantId
    {
        return ProductVariantId::create($this->id());
    }
    
    
    public function changeCombination(OptionAndOptionValueIds $combination): void
    {
        parent::changeCombination($combination);
        $this->raiseEvent(Deprecated\UpdatedProductVariantsCombination::create($this->createIdFromValue(),
                                                                               $combination));
    }
    
    
    public function changeSortOrder(int $sortOrder): void
    {
        parent::changeSortOrder($sortOrder);
        $this->raiseEvent(Deprecated\UpdatedProductVariantsSortOrder::create($this->createIdFromValue(), $sortOrder));
    }
    
    
    public function changeStock(ProductVariantStock $stock): void
    {
        parent::changeStock($stock);
        $this->raiseEvent(Deprecated\UpdatedProductVariantsStock::create($this->createIdFromValue(), $stock));
    }
    
    
    public function changeImageListId(ImageListId $imageListId): void
    {
        parent::changeImageListId($imageListId);
        $this->raiseEvent(Deprecated\UpdatedProductVariantsImageListId::create($this->createIdFromValue(),
                                                                               $imageListId));
    }
    
    
    public function changeProductCustomization(ProductCustomization $productCustomization): void
    {
        parent::changeProductCustomization($productCustomization);
        $this->raiseEvent(Deprecated\UpdatedProductVariantProductCustomization::create($this->createIdFromValue(),
                                                                                       $productCustomization));
    }
    
    
    public function changeProductIdentificationNumbers(ProductIdentificationNumbers $productIdentificationNumbers): void
    {
        parent::changeProductIdentificationNumbers($productIdentificationNumbers);
        $this->raiseEvent(Deprecated\UpdatedProductVariantProductIdentificationNumbers::create($this->createIdFromValue(),
                                                                                               $productIdentificationNumbers));
    }
}