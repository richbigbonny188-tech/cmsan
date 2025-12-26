<?php
/* --------------------------------------------------------------
 ConfigurationRepository.php 2021-05-14
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
 * Interface ConfigurationRepository
 *
 * @package Gambio\Core\Configuration\Services
 */
interface ConfigurationRepository
{
    /**
     * Searches for a configuration.
     *
     * @param string $key
     *
     * @return Configuration|null
     */
    public function find(string $key): ?Configuration;
    
    
    /**
     * Searches for a language dependent configuration.
     *
     * @param string $key
     * @param string $languageCode
     *
     * @return LanguageDependentConfiguration|null
     */
    public function findLanguageDependent(string $key, string $languageCode): ?LanguageDependentConfiguration;
    
    
    /**
     * Checks if configuration value for given key is available.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;
    
    
    /**
     * Checks if configuration for given key and language code is available.
     *
     * @param string $key
     * @param string $languageCode
     *
     * @return bool
     */
    public function hasLanguageDependent(string $key, string $languageCode): bool;
    
    
    /**
     * Updates the configuration.
     *
     * @param Configuration $configuration
     */
    public function update(Configuration $configuration): void;
    
    
    /**
     * Adds the configuration.
     *
     * @param Configuration $configuration
     */
    public function add(Configuration $configuration): void;
    
    
    /**
     * Updates the configuration.
     *
     * @param LanguageDependentConfiguration $configuration
     */
    public function updateLanguageDependent(LanguageDependentConfiguration $configuration): void;
    
    
    /**
     * Adds the configuration.
     *
     * @param LanguageDependentConfiguration $configuration
     */
    public function addLanguageDependent(LanguageDependentConfiguration $configuration): void;
    
    
    /**
     * Deletes configurations by key name.
     *
     * @param string ...$keys
     */
    public function delete(string ...$keys): void;
}