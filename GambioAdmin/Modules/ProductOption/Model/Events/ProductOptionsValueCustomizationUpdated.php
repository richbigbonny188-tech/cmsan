<?php
/*--------------------------------------------------------------------
 ProductOptionsValueCustomizationUpdated.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;

/**
 * Class ProductOptionsValueCustomizationUpdated
 *
 * @package    Gambio\Admin\Modules\ProductOption\Model\Events
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events\AdditionalOptionValueCustomizationUpdated
 */
class ProductOptionsValueCustomizationUpdated
{
    /**
     * @var AdditionalOptionId
     */
    private $productOptionId;
    
    /**
     * @var OptionValueCustomization
     */
    private $optionValueCustomization;
    
    
    /**
     * ProductOptionsValueCustomizationUpdated constructor.
     *
     * @param AdditionalOptionId       $productOptionId
     * @param OptionValueCustomization $optionValueCustomization
     */
    private function __construct(
        AdditionalOptionId       $productOptionId,
        OptionValueCustomization $optionValueCustomization
    ) {
        $this->productOptionId          = $productOptionId;
        $this->optionValueCustomization = $optionValueCustomization;
    }
    
    
    /**
     * @param AdditionalOptionId       $productOptionId
     * @param OptionValueCustomization $optionValueCustomization
     *
     * @return ProductOptionsValueCustomizationUpdated
     */
    public static function create(
        AdditionalOptionId       $productOptionId,
        OptionValueCustomization $optionValueCustomization
    ): ProductOptionsValueCustomizationUpdated {
        return new self($productOptionId, $optionValueCustomization);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function productOptionId(): AdditionalOptionId
    {
        return $this->productOptionId;
    }
    
    
    /**
     * @return OptionValueCustomization
     */
    public function optionValueCustomization(): OptionValueCustomization
    {
        return $this->optionValueCustomization;
    }
}