<?php
/* --------------------------------------------------------------
   ProductListingGroupSettingsRepository.php 2022-08-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\GroupSettings;

use Gambio\Shop\Modules\ProductListing\App\Exceptions\CustomerGroupSettingsNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemGroupSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingGroupSettingsRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingGroupSettingsRepository
{
    private ProductListingGroupSettingsReader $groupSettingsReader;
    private array                             $settingsCache = [];
    
    
    /**
     * ProductListingGroupSettingsRepository constructor.
     *
     * @param ProductListingGroupSettingsReader $groupSettingsReader
     */
    public function __construct(ProductListingGroupSettingsReader $groupSettingsReader)
    {
        $this->groupSettingsReader = $groupSettingsReader;
    }
    
    
    /**
     * Provides customer group settings.
     *
     * Consecutive calls with the same argument values return the same setting instance, so calling this
     * method multiple times with the same listing settings is cheap. Throws an exception if invalid settings
     * are provided.
     *
     * @param ListingSettings $listingSettings
     *
     * @return ListingItemGroupSettings
     * @throws CustomerGroupSettingsNotFoundException
     */
    public function getGroupSettings(ListingSettings $listingSettings): ListingItemGroupSettings
    {
        $cacheKey = md5("{$listingSettings->customerId()}__{$listingSettings->languageId()}");
        if (!array_key_exists($cacheKey, $this->settingsCache)) {
            $this->loadGroupSettings($cacheKey, $listingSettings);
        }
        
        return $this->settingsCache[$cacheKey];
    }
    
    
    /**
     * Loads customer group settings.
     *
     * @param string          $cacheKey
     * @param ListingSettings $listingSettings
     *
     * @throws CustomerGroupSettingsNotFoundException
     */
    private function loadGroupSettings(string $cacheKey, ListingSettings $listingSettings): void
    {
        $customerId = $listingSettings->customerId();
        $groupId    = $customerId ? $this->groupSettingsReader->fetchGroupId($customerId) : $this->groupSettingsReader->fetchGuestId();
        $data       = $this->groupSettingsReader->fetchGroupData($groupId,
                                                                 $listingSettings->languageId());
        
        $showNormalPriceOnDiscount = $this->groupSettingsReader->showNormalPriceOnDiscount();
        $showPriceBeforeSpecial    = $this->groupSettingsReader->showPriceBeforeSpecial();
        
        $this->settingsCache[$cacheKey] = new ListingItemGroupSettings($groupId,
                                                                               $data['isAllowedToSeePrices'],
                                                                               $data['isPersonalOfferEnabled'],
                                                                       $showNormalPriceOnDiscount,
                                                                               $data['isVariantsDiscountEnabled'],
                                                                       $showPriceBeforeSpecial,
                                                                               $data['groupDiscount']);
    }
}