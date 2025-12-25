<?php
/*--------------------------------------------------------------------
 AdditionalOptionsValueCustomizationUpdated.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;

/**
 * Class AdditionalOptionsValueCustomizationUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events
 */
class AdditionalOptionsValueCustomizationUpdated
{
    /**
     * AdditionalOptionsValueCustomizationUpdated constructor.
     *
     * @param AdditionalOptionId       $additionalOptionId
     * @param OptionValueCustomization $optionValueCustomization
     */
    private function __construct(
        private AdditionalOptionId       $additionalOptionId,
        private OptionValueCustomization $optionValueCustomization
    ) {
    }
    
    
    /**
     * @param AdditionalOptionId       $additionalOptionId
     * @param OptionValueCustomization $optionValueCustomization
     *
     * @return AdditionalOptionsValueCustomizationUpdated
     */
    public static function create(
        AdditionalOptionId       $additionalOptionId,
        OptionValueCustomization $optionValueCustomization
    ): AdditionalOptionsValueCustomizationUpdated {
        return new static($additionalOptionId, $optionValueCustomization);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function additionalOptionId(): AdditionalOptionId
    {
        return $this->additionalOptionId;
    }
    
    
    /**
     * @return OptionValueCustomization
     */
    public function optionValueCustomization(): OptionValueCustomization
    {
        return $this->optionValueCustomization;
    }
}