<?php
/* --------------------------------------------------------------
   HaendlerbundConfigurationFinder.php 2022-03-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\Classes;

use Gambio\Core\Configuration\Services\ConfigurationFinder;

class HaendlerbundConfigurationFinder
{
    private const PREFIX = 'modules/GambioHaendlerbundAdminModule/';
    private const API_KEY = '1IqJF0ap6GdDNF7HKzhFyciibdml8t4v';
    
    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;
    
    
    /**
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(ConfigurationFinder $configurationFinder)
    {
        $this->configurationFinder = $configurationFinder;
    }
    
    
    /**
     * @param string      $key
     * @param string|null $default
     *
     * @return string|null
     */
    public function get(string $key, string $default = null): ?string
    {
        $value = $this->configurationFinder->get(self::PREFIX . $key, $default);
        
        if ($key === 'apiKey' && empty($value)) {
            $value = static::API_KEY;
        }
        
        if ($key === 'mode' && empty($value)) {
            return 'productive';
        }
        
        return $value;
    }
    
    
    /**
     * @return bool
     */
    public function moduleIsInstalled(): bool
    {
        $installedConfig = (bool)$this->configurationFinder->get('gm_configuration/MODULE_CENTER_HAENDLERBUND_INSTALLED');
        
        return $installedConfig;
    }
    
    
    /**
     * @return bool
     */
    public function moduleIsActive(): bool
    {
        $activeConfig = (bool)$this->configurationFinder->get('modules/GambioHaendlerbundAdminModule/active');
        
        return $activeConfig;
    }
}
