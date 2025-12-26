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

namespace Gambio\Core\UserConfiguration\App;

use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\UserConfiguration\App\Exceptions\UserConfigurationNotFound;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserId;
use Gambio\Core\UserConfiguration\Services\CurrentUserConfigurationService as CurrentUserConfigurationServiceInterface;
use Gambio\Core\UserConfiguration\Services\Exceptions\NoLoggedInUserException;
use Gambio\Core\UserConfiguration\Services\UserConfigurationFactory;
use Gambio\Core\UserConfiguration\Services\UserConfigurationRepository;

/**
 * Class CurrentUserConfigurationService
 *
 * @package Gambio\Core\UserConfiguration\App
 */
class CurrentUserConfigurationService implements CurrentUserConfigurationServiceInterface
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
     * @var UserPreferences
     */
    private $userPreferences;
    
    /**
     * @var UserId|null
     */
    private $userId;
    
    
    /**
     * CurrentUserConfigurationService constructor.
     *
     * @param UserConfigurationRepository $repository
     * @param UserConfigurationFactory    $factory
     * @param UserPreferences             $userPreferences
     */
    public function __construct(
        UserConfigurationRepository $repository,
        UserConfigurationFactory $factory,
        UserPreferences $userPreferences
    ) {
        $this->repository      = $repository;
        $this->factory         = $factory;
        $this->userPreferences = $userPreferences;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getValue(string $key, string $default = null): ?string
    {
        try {
            $userConfiguration = $this->repository->getByKey($this->getCurrentUserId(),
                                                             $this->factory->createUserConfigurationKey($key));
            
            return $userConfiguration->value();
        } catch (UserConfigurationNotFound $e) {
            return $default;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeConfiguration(string $key, string $value): void
    {
        $this->repository->store($this->factory->createUserConfiguration($this->getCurrentUserId()->value(),
                                                                         $key,
                                                                         $value));
    }
    
    
    /**
     * @return UserId
     *
     * @throws NoLoggedInUserException
     */
    private function getCurrentUserId(): UserId
    {
        if ($this->userPreferences->userId() === null) {
            throw NoLoggedInUserException::forThisSession();
        }
        
        if ($this->userId === null) {
            $this->userId = $this->factory->createUserId($this->userPreferences->userId());
        }
        
        return $this->userId;
    }
}