<?php
/* --------------------------------------------------------------
  ProductListingControl.php 2023-03-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingControl
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects
 */
class ProductListingControl
{
    /**
     * @param ListingSettings   $settings
     * @param ListingPagination $pagination
     */
    public function __construct(
        private ListingSettings   $settings,
        private ListingPagination $pagination
    )
    {
    }


    /**
     * @return ListingSettings
     */
    public function settings(): ListingSettings
    {
        return $this->settings;
    }


    /**
     * @return ListingPagination
     */
    public function pagination(): ListingPagination
    {
        return $this->pagination;
    }
}