<?php
/* --------------------------------------------------------------
   ListingItemStockSettings.php 2022-07-15
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
 * Class ListingItemStockSettings
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemStockSettings
{
    private bool $isCheckoutAllowed;
    private bool $isStockCheckEnabled;
    private bool $isAttributeStockCheckEnabled;
    
    
    /**
     * ListingItemStockSettings constructor.
     *
     * @param bool $isCheckoutAllowed
     * @param bool $isStockCheckEnabled
     * @param bool $isAttributeStockCheckEnabled
     */
    public function __construct(bool $isCheckoutAllowed, bool $isStockCheckEnabled, bool $isAttributeStockCheckEnabled)
    {
        $this->isCheckoutAllowed            = $isCheckoutAllowed;
        $this->isStockCheckEnabled          = $isStockCheckEnabled;
        $this->isAttributeStockCheckEnabled = $isAttributeStockCheckEnabled;
    }
    
    
    /**
     * @return bool
     */
    public function isCheckoutAllowed(): bool
    {
        return $this->isCheckoutAllowed;
    }
    
    
    /**
     * @return bool
     */
    public function isStockCheckEnabled(): bool
    {
        return $this->isStockCheckEnabled;
    }
    
    
    /**
     * @return bool
     */
    public function isAttributeStockCheckEnabled(): bool
    {
        return $this->isAttributeStockCheckEnabled;
    }
}