<?php
/* --------------------------------------------------------------
   ProductListingPriceStockSettingsRepository.php 2022-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemStockSettings;

/**
 * Class ProductListingPriceStockSettingsRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingPriceStockSettingsRepository
{
    private const CONFIG_KEY_STOCK_ALLOW_CHECKOUT  = 'configuration/STOCK_ALLOW_CHECKOUT';
    private const CONFIG_KEY_STOCK_CHECK           = 'configuration/STOCK_CHECK';
    private const CONFIG_KEY_STOCK_CHECK_ATTRIBUTE = 'configuration/ATTRIBUTE_STOCK_CHECK';
    
    private ProductListingPriceStockSettingsReader $stockSettingsReader;
    private ?ListingItemStockSettings              $cachedSettings = null;
    
    
    /**
     * ProductListingPriceStockSettingsRepository constructor.
     *
     * @param ProductListingPriceStockSettingsReader $stockSettingsReader
     */
    public function __construct(ProductListingPriceStockSettingsReader $stockSettingsReader)
    {
        $this->stockSettingsReader = $stockSettingsReader;
    }
    
    
    /**
     * Provides listing item stock settings.
     *
     * @return ListingItemStockSettings
     */
    public function getStockSettings(): ListingItemStockSettings
    {
        if (null === $this->cachedSettings) {
            $isCheckoutAllowed            = $this->stockSettingsReader->getSetting(self::CONFIG_KEY_STOCK_ALLOW_CHECKOUT);
            $isStockCheckEnabled          = $this->stockSettingsReader->getSetting(self::CONFIG_KEY_STOCK_CHECK);
            $isAttributeStockCheckEnabled = $this->stockSettingsReader->getSetting(self::CONFIG_KEY_STOCK_CHECK_ATTRIBUTE);
            
            $this->cachedSettings = new ListingItemStockSettings($isCheckoutAllowed,
                                                                 $isStockCheckEnabled,
                                                                 $isAttributeStockCheckEnabled);
        }
        
        return $this->cachedSettings;
    }
}