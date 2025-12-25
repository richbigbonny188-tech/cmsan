<?php
/*--------------------------------------------------------------------------------------------------
    GmConfigurationAdapter.php 2021-10-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\Gambio\StyleEdit\Adapters;

use Gambio\Core\Configuration\Services\ConfigurationService;

class GmConfigurationAdapter extends ConfigurationAdapter
{
    const KEY_PREFIX = 'gm_configuration/';
    
    public static function create()
    {
        $configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        
        return new self($configurationService);
    }
    
    
    public function get(string $key)
    {
        return parent::get(self::KEY_PREFIX . $key);
    }
    
    
    public function set(string $key, $value): void
    {
        parent::set(self::KEY_PREFIX . $key, $value);
    }
}
