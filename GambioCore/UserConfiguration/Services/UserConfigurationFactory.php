<?php
/* --------------------------------------------------------------
   UserConfigurationFactory.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Services;

use Gambio\Core\UserConfiguration\Model\UserConfiguration;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserConfigurationKey;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserId;

/**
 * Class UserConfigurationFactory
 *
 * @package Gambio\Core\UserConfiguration\Services
 * @codeCoverageIgnore
 */
class UserConfigurationFactory
{
    /**
     * @param int $userId
     *
     * @return UserId
     */
    public function createUserId(int $userId): UserId
    {
        return UserId::create($userId);
    }
    
    
    /**
     * @param string $key
     *
     * @return UserConfigurationKey
     */
    public function createUserConfigurationKey(string $key): UserConfigurationKey
    {
        return UserConfigurationKey::create($key);
    }
    
    
    /**
     * @param int    $userId
     * @param string $key
     * @param string $value
     *
     * @return UserConfiguration
     */
    public function createUserConfiguration(int $userId, string $key, string $value): UserConfiguration
    {
        return UserConfiguration::create($this->createUserId($userId), $this->createUserConfigurationKey($key), $value);
    }
}