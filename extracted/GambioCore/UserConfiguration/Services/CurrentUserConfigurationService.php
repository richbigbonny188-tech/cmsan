<?php
/* --------------------------------------------------------------
   CurrentUserConfigurationService.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Services;

use Gambio\Core\UserConfiguration\Services\Exceptions\NoLoggedInUserException;

/**
 * Interface CurrentUserConfigurationService
 *
 * @package Gambio\Core\UserConfiguration\Services
 */
interface CurrentUserConfigurationService
{
    /**
     * Returns a user configuration value for the current user, based on the given key and default value.
     *
     * @param string      $key
     * @param string|null $default
     *
     * @return string|null
     *
     * @throws NoLoggedInUserException If no current user exists in the session of this request.
     */
    public function getValue(string $key, string $default = null): ?string;
    
    
    /**
     * Stores a user configuration based on the given key and value.
     *
     * @param string $key
     * @param string $value
     *
     * @throws NoLoggedInUserException If no current user exists in the session of this request.
     */
    public function storeConfiguration(string $key, string $value): void;
}