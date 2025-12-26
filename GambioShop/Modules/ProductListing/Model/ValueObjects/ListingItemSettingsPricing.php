<?php
/* --------------------------------------------------------------
  ListingItemSettingsPricing.php 2023-03-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemSettingsPricing
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemSettingsPricing
{
    private bool $isNormal;
    private bool $isPriceAvailableOnRequest;
    private bool $isNotAvailableForPurchase;


    /**
     * @param bool $isNormal
     * @param bool $isPriceAvailableOnRequest
     * @param bool $isNotAvailableForPurchase
     */
    public function __construct(
        bool $isNormal,
        bool $isPriceAvailableOnRequest,
        bool $isNotAvailableForPurchase
    )
    {
        $this->isNormal                  = $isNormal;
        $this->isPriceAvailableOnRequest = $isPriceAvailableOnRequest;
        $this->isNotAvailableForPurchase = $isNotAvailableForPurchase;
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'isNormal'                  => $this->isNormal,
            'isPriceAvailableOnRequest' => $this->isPriceAvailableOnRequest,
            'isNotAvailableForPurchase' => $this->isNotAvailableForPurchase,
        ];
    }
}