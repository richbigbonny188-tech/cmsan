<?php
/* --------------------------------------------------------------
   UserConfigurationRepository.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Services;

use Gambio\Core\UserConfiguration\App\Exceptions\UserConfigurationNotFound;
use Gambio\Core\UserConfiguration\Model\UserConfiguration;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserConfigurationKey;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserId;

/**
 * Interface UserConfigurationRepository
 *
 * @package Gambio\Core\UserConfiguration\Services
 */
interface UserConfigurationRepository
{
    /**
     * @param UserId               $userId
     * @param UserConfigurationKey $key
     *
     * @return UserConfiguration
     *
     * @throws UserConfigurationNotFound
     */
    public function getByKey(UserId $userId, UserConfigurationKey $key): UserConfiguration;
    
    
    /**
     * @param UserConfiguration $configuration
     */
    public function store(UserConfiguration $configuration): void;
}