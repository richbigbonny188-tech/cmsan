<?php
/* --------------------------------------------------------------
  DisplayService.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Exception;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ListingDisplaySettings;
use Gambio\Shop\Modules\ProductListing\Model\Entities\ListingItem;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class DisplayService
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class DisplayService
{
    /**
     * @param DisplaySettingsRepository $settingsRepository
     * @param DisplayMapper             $mapper
     */
    public function __construct(
        private DisplaySettingsRepository $settingsRepository,
        private DisplayMapper             $mapper
    )
    {
    }


    /**
     * Parses ProductListing Collection into multidimensional array prepared for frontend display.
     *
     * @param Listing         $listing
     * @param ListingSettings $listingSettings
     *
     * @return array
     * @throws Exception
     */
    public function getListing(
        Listing         $listing,
        ListingSettings $listingSettings
    ): array
    {
        $displaySettings   = $this->settingsRepository->getListingDisplaySettings($listingSettings);
        $listingItems      = iterator_to_array($listing);
        $listingItemsCount = count($listingItems);

        return array_map(
            function (
                ListingItem            $item,
                ListingDisplaySettings $settings
            ): array {
                return $this->mapper->mapOutput(
                    $item->toArray(),
                    $settings
                );
            },
            $listingItems,
            array_fill(0, $listingItemsCount, $displaySettings),
        );
    }
}