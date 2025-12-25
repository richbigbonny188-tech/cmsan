<?php
/* --------------------------------------------------------------
   DHLConfigurarationService.php 2021-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Services;

use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Configuration\Model\Interfaces\Configuration;

class DHLConfigurationService
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    private const PREFIX = 'modules/shipping/geschaeftskundenversand/';
    
    public function __construct(ConfigurationService $configurationService)
    {
    
        $this->configurationService = $configurationService;
    }
    
    /**
     * @param string $key
     *
     * @return string
     */
    public function get(string $key): string
    {
        $readConfiguration = $this->find($key);
        if ($readConfiguration === null) {
            return '';
        }
        
        return (string)$readConfiguration->value();
    }
    
    
    /**
     * @param string      $key
     * @param string|null $prefix
     *
     * @return Configuration|null
     */
    public function find(string $key, ?string $prefix = null): ?Configuration
    {
        $configurationPrefix = $prefix ?? static::PREFIX;
        
        return $this->configurationService->find($configurationPrefix . $key);
    }
}
