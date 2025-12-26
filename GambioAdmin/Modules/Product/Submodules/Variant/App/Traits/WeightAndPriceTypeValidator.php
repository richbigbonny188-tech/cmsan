<?php
/*--------------------------------------------------------------
   WeightAndPriceTypeValidator.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\Traits;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use InvalidArgumentException;

/**
 * Trait WeightAndPriceTypeValidator
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Traits
 */
trait WeightAndPriceTypeValidator
{
    /**
     * @param string $type
     *
     * @return string
     */
    private function getDatabaseTypeFieldValue(string $type): string
    {
        switch ($type) {
            case ProductCustomization::PRICE_TYPE_ADDITION :
            case ProductCustomization::WEIGHT_TYPE_ADDITION :
                return 'calc';
            
            case ProductCustomization::PRICE_TYPE_REPLACING :
            case ProductCustomization::WEIGHT_TYPE_REPLACING :
                return 'fix';
            
            default:
                throw new InvalidArgumentException('Unknown price or weight type. Got: ' . $type);
        }
    }
}