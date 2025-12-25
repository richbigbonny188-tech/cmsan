<?php
/* --------------------------------------------------------------
   UserConfigurationService.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Services;

/**
 * Interface UserConfigurationService
 *
 * @package Gambio\Core\UserConfiguration\Services
 */
interface UserConfigurationService
{
    /**
     * Returns a user configuration value based on the given user ID, key and default value.
     *
     * @param int         $userId
     * @param string      $key
     * @param string|null $default
     *
     * @return string|null
     */
    public function getValue(int $userId, string $key, string $default = null): ?string;
    
    
    /**
     * Stores a user configuration based on the given user ID, key and value.
     *
     * @param int    $userId
     * @param string $key
     * @param string $value
     */
    public function storeConfiguration(int $userId, string $key, string $value): void;
}