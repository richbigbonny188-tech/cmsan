<?php
/* --------------------------------------------------------------
   UserConfigurationServiceProvider.php 2021-05-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\UserConfiguration\App\Data\UserConfigurationReader;
use Gambio\Core\UserConfiguration\App\Data\UserConfigurationWriter;
use Gambio\Core\UserConfiguration\Services\CurrentUserConfigurationService;
use Gambio\Core\UserConfiguration\Services\UserConfigurationFactory;
use Gambio\Core\UserConfiguration\Services\UserConfigurationRepository;
use Gambio\Core\UserConfiguration\Services\UserConfigurationService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class UserConfigurationServiceProvider
 *
 * @package Gambio\Core\UserConfiguration
 */
class UserConfigurationServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CurrentUserConfigurationService::class,
            UserConfigurationService::class,
            UserConfigurationRepository::class,
            UserConfigurationFactory::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(UserConfigurationFactory::class);
        $this->application->registerShared(UserConfigurationReader::class)->addArgument(Connection::class);
        $this->application->registerShared(UserConfigurationWriter::class)->addArgument(Connection::class);
        
        $this->application->registerShared(UserConfigurationRepository::class,
                                           App\UserConfigurationRepository::class)
            ->addArgument(UserConfigurationReader::class)
            ->addArgument(UserConfigurationWriter::class)
            ->addArgument(UserConfigurationFactory::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(CurrentUserConfigurationService::class,
                                           App\CurrentUserConfigurationService::class)
            ->addArgument(UserConfigurationRepository::class)
            ->addArgument(UserConfigurationFactory::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(UserConfigurationService::class,
                                           App\UserConfigurationService::class)
            ->addArgument(UserConfigurationRepository::class)
            ->addArgument(UserConfigurationFactory::class);
    }
}