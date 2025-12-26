<?php
/* --------------------------------------------------------------
   RequestPriceUtilityTrait.php 2022-11-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Model\Request;

/**
 * Trait RequestPriceUtilityTrait
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model\Request
 */
trait RequestPriceUtilityTrait
{
    /**
     * Converts a float value to a string, using comma instead of point as separator.
     *
     * @param float $value
     *
     * @return string
     */
    private function convertFloatToString(float $value): string
    {
        return str_replace('.', ',', (string)$value);
    }
    
}