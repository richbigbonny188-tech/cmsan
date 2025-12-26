<?php
/* --------------------------------------------------------------
   ProductListingPriceStockSettingsReader.php 2022-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class ProductListingPriceStockSettingsReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingPriceStockSettingsReader
{
    private const ENABLED_SETTINGS_VALUES = [
        'true',
        '1',
        'enabled',
        'on',
        'yes',
        'ok',
    ];
    
    private ConfigurationFinder $configurationFinder;
    
    
    /**
     * ProductListingPriceStockSettingsReader constructor.
     *
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(ConfigurationFinder $configurationFinder)
    {
        $this->configurationFinder = $configurationFinder;
    }
    
    
    /**
     * Returns a setting for the given configuration key.
     * The settings value is either `true` or `false`, based configuration value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function getSetting(string $key): bool
    {
        $value = $this->configurationFinder->get($key);
        if (null === $value) {
            return false;
        }
        
        return $this->isTrue($value);
    }
    
    
    /**
     * Checks if value represents a true-ish boolean.
     *
     * @param string $value
     *
     * @return bool
     */
    private function isTrue(string $value): bool
    {
        return in_array(strtolower($value), self::ENABLED_SETTINGS_VALUES);
    }
}