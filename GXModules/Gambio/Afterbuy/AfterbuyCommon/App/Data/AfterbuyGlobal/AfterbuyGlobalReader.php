<?php
/* --------------------------------------------------------------
   AfterbuyGlobalReader.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\AfterbuyGlobal;

use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class AfterbuyGlobalReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyGlobal
 */
class AfterbuyGlobalReader
{
    private const STORAGE_AFTERBUY_NAMESPACE = 'modules/gambio/afterbuy';
    
    private const CONFIG_KEY_AFTERBUY_INSTALLED = 'gm_configuration/MODULE_CENTER_GAMBIOAFTERBUY_INSTALLED';
    
    private const CONFIG_KEY_AFTERBUY_ENABLED = 'active';
    
    /**
     * @var array|string[]
     */
    private static array $ENABLED_SETTINGS_VALUES = [
        'true',
        '1',
        'enabled',
        'on',
        'yes',
        'ok',
    ];
    
    
    /**
     * @var ConfigurationStorageRepository
     */
    private ConfigurationStorageRepository $storage;
    
    
    /**
     * @var ConfigurationFinder
     */
    private ConfigurationFinder $configurationFinder;
    
    
    /**
     * AfterbuyGlobalReaderBackup constructor.
     *
     * @param ConfigurationStorageRepositoryBuilder $storageRepositoryBuilder
     * @param ConfigurationFinder                   $configurationFinder
     */
    public function __construct(
        ConfigurationStorageRepositoryBuilder $storageRepositoryBuilder,
        ConfigurationFinder                   $configurationFinder
    ) {
        $this->storage             = $storageRepositoryBuilder->build(self::STORAGE_AFTERBUY_NAMESPACE);
        $this->configurationFinder = $configurationFinder;
    }
    
    
    /**
     * Checks if the afterbuy module is installed.
     *
     * @return bool
     */
    public function isInstalled(): bool
    {
        $isAfterbuyInstalled = $this->configurationFinder->get(self::CONFIG_KEY_AFTERBUY_INSTALLED);
        
        return $this->isTrue($isAfterbuyInstalled);
    }
    
    
    /**
     * Checks if the afterbuy module is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        $isAfterbuyEnabled = $this->storage->get(self::CONFIG_KEY_AFTERBUY_ENABLED);
        
        return $this->isTrue($isAfterbuyEnabled);
    }
    
    
    /**
     * @return string
     */
    public function fetchPartnerToken(): string
    {
        return $this->storage->get('partner_token');
    }
    
    
    /**
     * @return string
     */
    public function fetchAccountToken(): string
    {
        return $this->storage->get('account_token');
    }
    
    
    /**
     * Fetches afterbuy partner id from configuration.
     *
     * @return int
     */
    public function fetchPartnerId(): int
    {
        return (int)$this->storage->get('partner_id');
    }
    
    
    /**
     * Fetches afterbuy partner password from configuration.
     *
     * @return string
     */
    public function fetchPartnerPassword(): string
    {
        return $this->storage->get('partner_password');
    }
    
    
    /**
     * Fetches afterbuy user id from configuration.
     *
     * @return string
     */
    public function fetchUserId(): string
    {
        return $this->storage->get('user_id');
    }
    
    
    /**
     * Fetches afterbuy user password from configuration.
     *
     * @return string
     */
    public function fetchUserPassword(): string
    {
        return $this->storage->get('partner_password');
    }
    
    
    /**
     * @return string
     */
    public function fetchDetailLevel(): string
    {
        // hardcoded, because it is the same value as in the example and there is no explanation what it does.
        return '0';
    }
    
    
    /**
     * @return string
     */
    public function fetchErrorLanguage(): string
    {
        // hardcoded, because of laziness and it is the same as in the example request.
        return 'DE';
    }
    
    
    /**
     * Fetches the log level configuration from the afterbuy module.
     *
     * @return string
     */
    public function fetchLogLevel(): string
    {
        return $this->storage->get('minimum_log_level');
    }
    
    
    /**
     * Checks if value represents a true-ish boolean.
     *
     * @param string|null $value
     *
     * @return bool
     */
    private function isTrue(?string $value): bool
    {
        if (null === $value) {
            return false;
        }
        
        return in_array(strtolower($value), self::$ENABLED_SETTINGS_VALUES, true);
    }
}