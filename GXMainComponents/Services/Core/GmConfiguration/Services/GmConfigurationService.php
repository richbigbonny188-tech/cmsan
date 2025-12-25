<?php
/* --------------------------------------------------------------
  GmConfigurationService.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class GmConfigurationService
 */
class GmConfigurationService implements GmConfigurationServiceInterface
{
    private const PREFIX = 'gm_configuration/';
    
    /**
     * @var ConfigurationService
     */
    protected $configurationService;
    
    
    /**
     * GmConfigurationService constructor.
     *
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * @param string $key
     *
     * @return GmConfigurationInterface
     * @throws GmConfigurationNotFoundException
     */
    public function getConfigurationByKey(string $key): GmConfigurationInterface
    {
        $key           = $this->prefixKey($key, self::PREFIX);
        $configuration = $this->configurationService->find($key);
        
        if (!$configuration) {
            throw new GmConfigurationNotFoundException("Configuration with key ({$key}) not found.");
        }
        
        return new GmConfiguration(0, $configuration->key(), $configuration->value(), 0, $configuration->sortOrder());
    }
    
    
    /**
     * @param GmConfigurationInterface $configuration
     */
    public function updateGmConfiguration(GmConfigurationInterface $configuration): void
    {
        $key = $this->prefixKey($configuration->key(), self::PREFIX);
        $this->configurationService->save($key, $configuration->value());
    }
    
    
    /**
     * Prefix the key if there is currently no prefix.
     *
     * @param string $key
     * @param string $prefix
     *
     * @return string
     */
    protected function prefixKey(string $key, string $prefix): string
    {
        return strpos($key, $prefix) === 0 ? $key : "{$prefix}{$key}";
    }
}