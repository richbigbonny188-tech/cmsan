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

namespace Gambio\Core\UserConfiguration\App;

use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\UserConfiguration\App\Data\UserConfigurationReader;
use Gambio\Core\UserConfiguration\App\Data\UserConfigurationWriter;
use Gambio\Core\UserConfiguration\App\Exceptions\UserConfigurationNotFound;
use Gambio\Core\UserConfiguration\Model\Events\UserConfigurationCreated;
use Gambio\Core\UserConfiguration\Model\UserConfiguration;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserConfigurationKey;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserId;
use Gambio\Core\UserConfiguration\Services\UserConfigurationFactory;
use Gambio\Core\UserConfiguration\Services\UserConfigurationRepository as UserConfigurationRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class UserConfigurationRepository
 *
 * @package Gambio\Core\UserConfiguration\App
 */
class UserConfigurationRepository extends AbstractEventDispatchingRepository
    implements UserConfigurationRepositoryInterface
{
    /**
     * @var UserConfigurationReader
     */
    private $reader;
    
    /**
     * @var UserConfigurationWriter
     */
    private $writer;
    
    /**
     * @var UserConfigurationFactory
     */
    private $factory;
    
    
    /**
     * UserConfigurationRepository constructor.
     *
     * @param UserConfigurationReader  $reader
     * @param UserConfigurationWriter  $writer
     * @param UserConfigurationFactory $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        UserConfigurationReader $reader,
        UserConfigurationWriter $writer,
        UserConfigurationFactory $factory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->reader  = $reader;
        $this->writer  = $writer;
        $this->factory = $factory;
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @param UserId               $userId
     * @param UserConfigurationKey $key
     *
     * @return UserConfiguration
     *
     * @throws UserConfigurationNotFound
     */
    public function getByKey(UserId $userId, UserConfigurationKey $key): UserConfiguration
    {
        $value = $this->reader->getConfigurationValue($userId, $key);
        
        return $this->factory->createUserConfiguration($userId->value(), $key->value(), $value);
    }
    
    
    /**
     * @param UserConfiguration $configuration
     */
    public function store(UserConfiguration $configuration): void
    {
        $userId = $this->factory->createUserId($configuration->userId());
        $key    = $this->factory->createUserConfigurationKey($configuration->key());
        
        try {
            $userConfiguration = $this->getByKey($userId, $key);
            $userConfiguration->changeValue($configuration->value());
            
            $this->writer->store($configuration);
            $this->dispatchEntityEvents($userConfiguration);
        } catch (UserConfigurationNotFound $e) {
            $this->writer->store($configuration);
            $this->dispatchEvent(UserConfigurationCreated::create($userId, $key, $configuration->value()));
        }
    }
}
