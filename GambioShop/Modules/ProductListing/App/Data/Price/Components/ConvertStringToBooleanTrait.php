<?php
/* --------------------------------------------------------------
   ConvertStringToBooleanTrait.php 2022-08-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

/**
 * Trait ConvertStringToBooleanTrait
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
trait ConvertStringToBooleanTrait
{
    private static array $ENABLED_SETTINGS_VALUES = [
        'true',
        '1',
        'enabled',
        'on',
        'yes',
        'ok',
    ];
    
    
    /**
     * Checks if value represents a true-ish boolean.
     *
     * @param string $value
     *
     * @return bool
     */
    private function isTrue(string $value): bool
    {
        return in_array(strtolower($value), self::$ENABLED_SETTINGS_VALUES, true);
    }
}