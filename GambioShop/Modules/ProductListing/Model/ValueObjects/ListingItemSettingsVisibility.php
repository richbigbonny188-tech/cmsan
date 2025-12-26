<?php
/* --------------------------------------------------------------
   ListingItemSettingsVisibility.php 2022-05-09
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
 * Class ListingItemSettingsVisibility
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemSettingsVisibility
{
    private bool $showStock;
    private bool $showVpeUnit;
    private bool $showOnStartPage;
    private bool $showWeight;
    private bool $showSitemap;
    private bool $showReleaseDate;
    private bool $showFreeShipping;


    /**
     * ListingItemSettingsVisibility constructor.
     *
     * @param bool $showStock
     * @param bool $showVpeUnit
     * @param bool $showOnStartPage
     * @param bool $showWeight
     * @param bool $showSitemap
     * @param bool $showReleaseDate
     * @param bool $showFreeShipping
     */
    public function __construct(
        bool $showStock,
        bool $showVpeUnit,
        bool $showOnStartPage,
        bool $showWeight,
        bool $showSitemap,
        bool $showReleaseDate,
        bool $showFreeShipping
    ) {
        $this->showStock        = $showStock;
        $this->showVpeUnit      = $showVpeUnit;
        $this->showOnStartPage  = $showOnStartPage;
        $this->showWeight       = $showWeight;
        $this->showSitemap      = $showSitemap;
        $this->showReleaseDate  = $showReleaseDate;
        $this->showFreeShipping = $showFreeShipping;
    }


    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'showStock'        => $this->showStock,
            'showVpeUnit'      => $this->showVpeUnit,
            'showOnStartPage'  => $this->showOnStartPage,
            'showWeight'       => $this->showWeight,
            'showSitemap'      => $this->showSitemap,
            'showReleaseDate'  => $this->showReleaseDate,
            'showFreeShipping' => $this->showFreeShipping,
        ];
    }
}
