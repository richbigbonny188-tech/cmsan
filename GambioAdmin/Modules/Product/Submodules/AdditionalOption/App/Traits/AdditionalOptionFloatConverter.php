<?php
/*--------------------------------------------------------------------
 AdditionalOptionFloatConverter.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Traits;

/**
 * Trait AdditionalOptionFloatConverter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Traits
 */
trait AdditionalOptionFloatConverter
{
    /**
     * @param float $value
     *
     * @return array{value: float, prefix: string}
     */
    protected function convertFloatToPositiveFloatAndPrefix(float $value): array
    {
        return [
            'prefix' => $value >= 0 ? '+' : '-',
            'value'  => $value >= 0 ? $value : $value * -1,
        ];
    }
    
    
    /**
     * @param string $prefix
     * @param float  $value
     *
     * @return float
     */
    protected function convertPositiveFloatAndPrefixToFloat(
        string $prefix,
        float  $value
    ): float {
        return ($prefix === '+' ? 1 : -1) * $value;
    }
}