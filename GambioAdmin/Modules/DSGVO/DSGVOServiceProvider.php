<?php
/* --------------------------------------------------------------
 DSGVOServiceProvider.php 2021-05-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DSGVO;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\UserConfiguration\Services\UserConfigurationService;

/**
 * Class DSGVOServiceProvider
 * @package Gambio\Admin\Modules\DSGVO
 */
class DSGVOServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            Services\DSGVOService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerService();
        $this->registerServiceComponents();
    }
    
    
    /**
     * Registers the DSGVO service.
     */
    private function registerService(): void
    {
        $this->application->registerShared(Services\DSGVOService::class, App\DSGVOService::class)->addArguments([
                                                                                                                    Services\DSGVORepository::class,
                                                                                                                    Services\DSGVOLogger::class,
                                                                                                                    UserPreferences::class,
                                                                                                                    UserConfigurationService::class
                                                                                                                ]);
    }
    
    
    /**
     * Registers the DSGVO service components.
     */
    private function registerServiceComponents(): void
    {
        $this->application->registerShared(Services\DSGVORepository::class, App\Data\DSGVORepository::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(Services\DSGVOLogger::class, App\Data\DSGVOLogger::class);
    }
}