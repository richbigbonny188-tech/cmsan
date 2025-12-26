<?php
/* --------------------------------------------------------------
 ConfigurationServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Configuration\Builder\ConfigurationFinderBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ConfigurationServiceProvider
 * @package Gambio\Core\Configuration
 */
class ConfigurationServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ConfigurationService::class,
            ConfigurationFinder::class,
            ConfigurationFinderBuilder::class,
            
            Services\ConfigurationService::class,
            Services\ConfigurationFinder::class,
            App\Creation\ConfigurationFinderBuilder::class,
            
            Compatibility\ConfigurationStorageRepositoryBuilder::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->attachEventListener(Model\Events\GroupCheckUpdated::class,
                                                App\EventListeners\GroupPermissionListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerConfigurationService();
        $this->registerConfigurationFinders();
        $this->registerConfigurationRepository();
        $this->registerConfigurationComponents();
        $this->registerConfigurationStorage();
        $this->registerEventListeners();
    }
    
    
    /**
     * Registers the configuration service.
     */
    private function registerConfigurationService(): void
    {
        $this->application->registerShared(Services\ConfigurationService::class, App\ConfigurationService::class)
            ->addArgument(Services\ConfigurationRepository::class)
            ->addArgument(App\Creation\ConfigurationFactory::class);
        $this->application->registerShared(ConfigurationService::class, App\ConfigurationService::class)
            ->addArgument(Services\ConfigurationRepository::class)
            ->addArgument(App\Creation\ConfigurationFactory::class);
    }
    
    
    private function registerConfigurationFinders(): void
    {
        $this->application->registerShared(Services\ConfigurationFinder::class, App\ConfigurationFinder::class)
            ->addArgument(Services\ConfigurationService::class);
        $this->application->registerShared(ConfigurationFinder::class, App\ConfigurationFinder::class)
            ->addArgument(Services\ConfigurationService::class);
        $this->application->registerShared(App\Creation\ConfigurationFinderBuilder::class)
            ->addArgument(Services\ConfigurationFinder::class);
        
        $this->application->registerShared(ConfigurationFinderBuilder::class)
            ->addArgument(Services\ConfigurationFinder::class);
    }
    
    
    /**
     * Registers the configuration storage.
     */
    private function registerConfigurationStorage(): void
    {
        $this->application->registerShared(Compatibility\ConfigurationStorageRepositoryBuilder::class)
            ->addArgument(Connection::class)
            ->addArgument(Compatibility\Repositories\Storage\NamespaceConverter::class);
        $this->application->registerShared(Compatibility\Repositories\Storage\NamespaceConverter::class);
    }
    
    
    /**
     * Registers the configuration repository.
     */
    private function registerConfigurationRepository(): void
    {
        $this->application->registerShared(Services\ConfigurationRepository::class,
                                           App\Data\ConfigurationRepository::class)
            ->addArgument(App\Data\ConfigurationReader::class)
            ->addArgument(App\Data\ConfigurationWriter::class)
            ->addArgument(App\Creation\ConfigurationFactory::class);
    }
    
    
    /**
     * Registers the configuration reader, writer, factory, mapper and options resolver.
     */
    private function registerConfigurationComponents(): void
    {
        $this->application->registerShared(App\Data\ConfigurationReader::class)->addArgument(Connection::class);
        $this->application->registerShared(App\Data\ConfigurationWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(EventDispatcherInterface::class);
        $this->application->registerShared(App\Creation\ConfigurationFactory::class);
    }
    
    
    public function registerEventListeners(): void
    {
        $this->application->registerShared(App\EventListeners\GroupPermissionListener::class)
            ->addArgument(Services\ConfigurationService::class)
            ->addArgument(Connection::class);
    }
}