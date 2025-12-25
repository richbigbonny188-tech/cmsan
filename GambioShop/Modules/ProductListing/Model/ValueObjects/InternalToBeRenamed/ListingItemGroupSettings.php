<?php
/* --------------------------------------------------------------
   ListingItemGroupSettings.php 2022-08-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed;

/**
 * Class ListingItemGroupSettings
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemGroupSettings
{
    private int   $groupId;
    private bool  $isAllowedToSeePrices;
    private bool  $isPersonalOfferEnabled;
    private bool  $showNormalPriceOnDiscount;
    private bool  $isVariantsDiscountEnabled;
    private bool  $showPriceBeforeSpecial;
    private float $groupDiscount;
    
    
    /**
     * ListingItemGroupSettings constructor.
     *
     * @param int   $groupId
     * @param bool  $isAllowedToSeePrices
     * @param bool  $isPersonalOfferEnabled
     * @param bool  $showNormalPriceOnDiscount
     * @param bool  $isVariantsDiscountEnabled
     * @param bool  $showPriceBeforeSpecial
     * @param float $groupDiscount
     */
    public function __construct(
        int   $groupId,
        bool  $isAllowedToSeePrices,
        bool  $isPersonalOfferEnabled,
        bool  $showNormalPriceOnDiscount,
        bool  $isVariantsDiscountEnabled,
        bool  $showPriceBeforeSpecial,
        float $groupDiscount
    ) {
        $this->groupId                   = $groupId;
        $this->isAllowedToSeePrices      = $isAllowedToSeePrices;
        $this->isPersonalOfferEnabled    = $isPersonalOfferEnabled;
        $this->showNormalPriceOnDiscount = $showNormalPriceOnDiscount;
        $this->isVariantsDiscountEnabled = $isVariantsDiscountEnabled;
        $this->showPriceBeforeSpecial    = $showPriceBeforeSpecial;
        $this->groupDiscount             = $groupDiscount;
    }
    
    
    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId;
    }
    
    
    /**
     * @return bool
     */
    public function isAllowedToSeePrices(): bool
    {
        return $this->isAllowedToSeePrices;
    }
    
    
    /**
     * @return bool
     */
    public function isPersonalOfferEnabled(): bool
    {
        return $this->isPersonalOfferEnabled;
    }
    
    
    /**
     * @return bool
     */
    public function showNormalPriceOnDiscount(): bool
    {
        return $this->showNormalPriceOnDiscount;
    }
    
    
    /**
     * @return bool
     */
    public function isVariantsDiscountEnabled(): bool
    {
        return $this->isVariantsDiscountEnabled;
    }
    
    
    /**
     * @return bool
     */
    public function showPriceBeforeSpecial(): bool
    {
        return $this->showPriceBeforeSpecial;
    }
    
    
    /**
     * @return float
     */
    public function groupDiscount(): float
    {
        return $this->groupDiscount;
    }
}