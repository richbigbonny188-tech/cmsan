<?php
/*--------------------------------------------------------------------
 ProductDownloadsValueCustomizationUpdated.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductDownloadsValueCustomizationUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Events
 */
class ProductDownloadsValueCustomizationUpdated
{
    /**
     * ProductDownloadsValueCustomizationUpdated constructor.
     *
     * @param AdditionalOptionId          $productOptionId
     * @param OptionValueCustomization $optionValueCustomization
     */
    private function __construct(
        private AdditionalOptionId       $productOptionId,
        private OptionValueCustomization $optionValueCustomization
    ) {
    }
    
    
    /**
     * @param AdditionalOptionId          $productOptionId
     * @param OptionValueCustomization $optionValueCustomization
     *
     * @return ProductDownloadsValueCustomizationUpdated
     */
    public static function create(
        AdditionalOptionId       $productOptionId,
        OptionValueCustomization $optionValueCustomization
    ): ProductDownloadsValueCustomizationUpdated {
        return new static($productOptionId, $optionValueCustomization);
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