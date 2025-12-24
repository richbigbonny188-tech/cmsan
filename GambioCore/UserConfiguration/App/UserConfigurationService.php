<?php
/* --------------------------------------------------------------
   UserConfigurationService.php 2021-05-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\App;

use Gambio\Core\UserConfiguration\App\Exceptions\UserConfigurationNotFound;
use Gambio\Core\UserConfiguration\Services\UserConfigurationFactory;
use Gambio\Core\UserConfiguration\Services\UserConfigurationRepository;
use Gambio\Core\UserConfiguration\Services\UserConfigurationService as UserConfigurationServiceInterface;

/**
 * Class UserConfigurationService
 *
 * @package Gambio\Core\UserConfiguration\App
 */
class UserConfigurationService implements UserConfigurationServiceInterface
{
    /**
     * @var UserConfigurationRepository
     */
    private $repository;
    
    /**
     * @var UserConfigurationFactory
     */
    private $factory;
    
    
    /**
     * CurrentUserConfigurationService constructor.
     *
     * @param UserConfigurationRepository $repository
     * @param UserConfigurationFactory    $factory
     */
    public function __construct(UserConfigurationRepository $repository, UserConfigurationFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getValue(int $userId, string $key, string $default = null): ?string
    {
        try {
            $userConfiguration = $this->repository->getByKey($this->factory->createUserId($userId),
                                                             $this->factory->createUserConfigurationKey($key));
            
            return $userConfiguration->value();
        } catch (UserConfigurationNotFound $e) {
            return $default;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeConfiguration(int $userId, string $key, string $value): void
    {
        $this->repository->store($this->factory->createUserConfiguration($userId, $key, $value));
    }
}