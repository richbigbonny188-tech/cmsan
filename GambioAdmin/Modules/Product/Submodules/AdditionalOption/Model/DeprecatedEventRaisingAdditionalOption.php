<?php
/*--------------------------------------------------------------
   DeprecatedEventRaisingAdditionalOption.php 2023-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionsImageListIdUpdated;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionsSortOrderUpdated;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionsStockUpdated;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionsValueCustomizationUpdated;
use Gambio\Admin\Modules\ProductOption\Model\ProductOption;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionId;

/**
 * Class DeprecatedEventRaisingAdditionalOption
 *
 * @package    Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model
 * @deprecated will be removed with GX 4.11. This class is used to bridge the old ProductOptions
 *             and the refactored AdditionalOptions for the duration of GX 4.9
 */
class DeprecatedEventRaisingAdditionalOption extends ProductOption
{
    /**
     * @return ProductOptionId
     */
    private function createIdFromValue(): ProductOptionId
    {
        return ProductOptionId::create($this->id());
    }
    
    
    public function changeImageListId(ImageListId $imageListId): void
    {
        parent::changeImageListId($imageListId);
        $this->raiseEvent(ProductOptionsImageListIdUpdated::create($this->createIdFromValue(), $imageListId));
    }
    
    
    public function changeOptionValueCustomization(OptionValueCustomization $optionValueCustomization): void
    {
        parent::changeOptionValueCustomization($optionValueCustomization);
        $this->raiseEvent(ProductOptionsValueCustomizationUpdated::create($this->createIdFromValue(),
                                                                          $optionValueCustomization));
    }
    
    
    public function changeAdditionalOptionStock(AdditionalOptionStock $additionalOptionStock): void
    {
        parent::changeAdditionalOptionStock($additionalOptionStock);
        $this->raiseEvent(ProductOptionsStockUpdated::create($this->createIdFromValue(), $additionalOptionStock));
    }
    
    
    public function changeSortOrder(int $sorOrder): void
    {
        parent::changeSortOrder($sorOrder);
        $this->raiseEvent(ProductOptionsSortOrderUpdated::create($this->createIdFromValue(), $sorOrder));
    }
}