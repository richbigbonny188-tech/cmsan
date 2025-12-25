<?php
/* --------------------------------------------------------------
   ListingItemSettings.php 2023-12-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemSettings
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemSettings
{
    /**
     * ListingItemSettings constructor.
     *
     * @param string                         $type
     * @param float                          $weight
     * @param bool                           $isFsk18
     * @param ListingItemDownloadInformation $downloadInformation
     * @param ListingItemSettingsVpe|null    $vpe
     * @param ListingItemSettingsVisibility  $visibility
     * @param ListingItemSettingsPricing     $pricing
     */
    public function __construct(
        private string                         $type,
        private float                          $weight,
        private bool                           $isFsk18,
        private ListingItemDownloadInformation $downloadInformation,
        private ?ListingItemSettingsVpe        $vpe,
        private ListingItemSettingsVisibility  $visibility,
        private ListingItemSettingsPricing     $pricing
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
            'type'           => $this->type,
            'weight'         => $this->weight,
            'isFsk18'        => $this->isFsk18,
            'isDownloadable' => $this->downloadInformation->isDownloadable(),
            'vpe'            => $this->vpe?->toArray(),
            'visibility'     => $this->visibility->toArray(),
            'pricing'        => $this->pricing->toArray(),
        ];
    }
}