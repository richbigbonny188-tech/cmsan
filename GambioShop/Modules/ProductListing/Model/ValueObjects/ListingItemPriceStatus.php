<?php
/* --------------------------------------------------------------
   ListingItemPriceStatus.php 2022-07-21
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
 * Class ListingItemPriceStatus
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemPriceStatus
{
    private const STATUS_NORMAL                     = 0;
    private const STATUS_PRICE_ON_REQUEST           = 1;
    private const STATUS_NOT_AVAILABLE_FOR_PURCHASE = 2;
    
    private const VALID_PRICE_STATUS = [
        self::STATUS_NORMAL,
        self::STATUS_PRICE_ON_REQUEST,
        self::STATUS_NOT_AVAILABLE_FOR_PURCHASE,
    ];
    
    private int $statusFlag;
    
    
    /**
     * ListingItemPriceStatus constructor.
     *
     * @param int $statusFlag
     */
    public function __construct(int $statusFlag)
    {
        $statusFlag       = in_array($statusFlag, self::VALID_PRICE_STATUS) ? $statusFlag : self::STATUS_NORMAL;
        $this->statusFlag = $statusFlag;
    }
    
    
    /**
     * @return bool
     */
    public function isNormal(): bool
    {
        return $this->statusFlag === self::STATUS_NORMAL;
    }
    
    
    /**
     * @return bool
     */
    public function isPriceOnRequest(): bool
    {
        return $this->statusFlag === self::STATUS_PRICE_ON_REQUEST;
    }
    
    
    /**
     * @return bool
     */
    public function isNotAvailableForPurchase(): bool
    {
        return $this->statusFlag === self::STATUS_NOT_AVAILABLE_FOR_PURCHASE;
    }
}