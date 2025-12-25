<?php
/* --------------------------------------------------------------
 ConfigurationStorageRepository.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 14 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Compatibility;

/**
 * Interface ConfigurationStorageRepository
 *
 * @package Gambio\Core\Configuration\Compatibility
 */
interface ConfigurationStorageRepository
{
    /**
     * Returns the configuration value of the given key.
     *
     * @param string $key
     *
     * @return string
     */
    public function get(string $key): string;
    
    
    /**
     * Returns a list of all configurations in the current namespace.
     *
     * Additionally, it is possible to use a prefix for further filtering.
     * The format of the returned array is flat (one dimensional array).
     *
     * @param string $prefix
     *
     * @return array
     */
    public function getAll(string $prefix = ''): array;
    
    
    /**
     * Returns a list of all configurations in the current namespace.
     *
     * Additionally, it is possible to use a prefix for further filtering.
     * The format of the returned array is a tree (multi dimensional array).
     *
     * @param string $prefix
     *
     * @return array
     */
    public function getAllTree(string $prefix = ''): array;
    
    
    /**
     * Checks if configuration value of given key represents true boolean.
     *
     * @param string $key
     *
     * @return bool
     */
    public function is(string $key): bool;
    
    
    /**
     * Sets a configuration with the given value.
     *
     * @param string $key
     * @param string $value
     */
    public function set(string $key, string $value): void;
    
    
    /**
     * Updates a list of configurations.
     *
     * The data array will be flattened, so it is possible to use a tree array.
     *
     * @param array $configTree
     */
    public function setAll(array $configTree): void;
    
    
    /**
     * Deletes a configuration from namespace by using the given key.
     *
     * @param string $key
     */
    public function delete(string $key): void;
    
    
    /**
     * Deletes all configurations from namespace.
     *
     * It is possible to restrict the delete command by providing a prefix.
     *
     * @param string $prefix
     */
    public function deleteAll(string $prefix = ''): void;
}