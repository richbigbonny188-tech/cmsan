<?php
/* --------------------------------------------------------------
   ListingItemSettingsVpe.php 2023-06-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemSettingsVpe
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemSettingsVpe
{
    /**
     * ListingItemSettingsVpe constructor.
     *
     * @param float  $value
     * @param string $unit
     * @param string $formatted
     */
    public function __construct(
        private float $value,
        private string $unit,
        private string $formatted
    ) {
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'formatted' => $this->formatted,
            'value'     => $this->value,
            'unit'      => $this->unit,
        ];
    }
}