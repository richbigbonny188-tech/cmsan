<?php
/* --------------------------------------------------------------
 ConfigurationService.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Services;

use Gambio\Core\Configuration\Model\Interfaces\Configuration;
use Gambio\Core\Configuration\Model\Interfaces\LanguageDependentConfiguration;

/**
 * Interface ConfigurationService
 *
 * @package Gambio\Core\Configuration\Services
 */
interface ConfigurationService
{
    /**
     * Searches for a configuration.
     *
     * This method can return null, if no configuration was found with the provided key.
     * Using ::has first ensures to find a valid, configuration.
     *
     * @param string $key Configuration key.
     *
     * @return Configuration
     * @see ConfigurationService::has
     */
    public function find(string $key): ?Configuration;
    
    
    /**
     * Searches for a language dependent configuration.
     *
     * This method can return null, if no configuration was found with the provided key and language code.
     * Using ::hasLanguageDependent first ensures to find a valid, configuration.
     *
     * @param string $key
     * @param string $languageCode
     *
     * @return LanguageDependentConfiguration|null
     * @see ConfigurationService::hasLanguageDependent
     */
    public function findLanguageDependent(string $key, string $languageCode): ?LanguageDependentConfiguration;
    
    
    /**
     * Checks if a configuration with the given key exists.
     *
     * @param string $key Configuration key.
     *
     * @return bool
     */
    public function has(string $key): bool;
    
    
    /**
     * Checks if a configuration with the given key and language code exists.
     *
     * @param string $key
     * @param string $languageCode
     *
     * @return bool
     */
    public function hasLanguageDependent(string $key, string $languageCode): bool;
    
    
    /**
     * Saves a configuration item.
     *
     * This function either adds a configuration, if it not exists or updates
     * an existing configuration.
     *
     * @param string      $key
     * @param string|null $value
     */
    public function save(string $key, ?string $value): void;
    
    
    /**
     * Saves the configuration items.
     *
     * This function either adds the configuration, if it not exists or updates
     * an existing configuration. It is possible to provide multiple configurations.
     *
     * @param array $configurations
     */
    public function saveBulk(array $configurations): void;
    
    
    /**
     * Saves language dependent configuration item.
     *
     * This function either adds a configuration, if it not exists or updates
     * an existing configuration.
     *
     * @param string      $key
     * @param string      $languageCode
     * @param string|null $value
     */
    public function saveLanguageDependent(string $key, string $languageCode, ?string $value): void;
    
    
    /**
     * Saves language dependent configuration items.
     *
     * This function either adds the configuration, if it not exists or updates
     * an existing configuration. It is possible to provide multiple configurations.
     *
     * The parameter $configurations must be a two-dimensional array and the inner one must contain
     * the keys "key", "value", "languageCode".
     *
     * @param array $configurations
     */
    public function saveLanguageDependentBulk(array $configurations): void;
    
    
    /**
     * Deletes configurations.
     *
     * This function deletes all configuration items with the given keys.
     * If any provided key not exists, the method will silently fail and continue.
     *
     * @param string ...$keys
     */
    public function delete(string ...$keys): void;
}
