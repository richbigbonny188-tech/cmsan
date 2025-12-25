<?php
/* --------------------------------------------------------------
 HubServiceProvider.php 2023-02-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

namespace GXModules\Gambio\Hub\Admin\Module;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Modules\TrackingCode\Model\Events\TrackingCodeCreated;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository;
use Gambio\Core\Application\DependencyInjection\AbstractModuleBootableServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Admin\Layout\Renderer\GambioAdminLoader;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Core\TextManager\Services\TextManager;
use GXModules\Gambio\Hub\Admin\Classes\Extensions\TrackingCodeCreatedListener;
use GXModules\Gambio\Hub\Admin\Filter\HubMenuFilterOneConfigActive;

/**
 * Class HubServiceProvider
 *
 * @package GXModules\Gambio\Hub\Admin\Module
 */
class HubServiceProvider extends AbstractModuleBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            HubFooterBadgeLoader::class,
            HubTranslationsLoader::class,
            HubMenuFilterOneConfigActive::class,
            TrackingCodeCreatedListener::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(HubFooterBadgeLoader::class)->addArgument(ConfigurationFinder::class);
        $this->application->registerShared(HubTranslationsLoader::class)->addArgument(TextManager::class);
        $this->application->registerShared(HubMenuFilterOneConfigActive::class)
            ->addArgument(ConfigurationService::class);
        $this->application->registerShared(TrackingCodeCreatedListener::class)
            ->addArguments([TrackingCodeRepository::class, Connection::class, ConfigurationFinder::class]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(GambioAdminLoader::class)->invokeMethod('addLoader', [HubFooterBadgeLoader::class]);
        $this->application->inflect(GambioAdminLoader::class)
            ->invokeMethod('addLoader', [HubTranslationsLoader::class]);
        $this->application->inflect(FilterFactory::class)
            ->invokeMethod('addFilter',
                           [HubMenuFilterOneConfigActive::FILTER_METHOD, HubMenuFilterOneConfigActive::class]);
        $this->application->attachEventListener(TrackingCodeCreated::class, TrackingCodeCreatedListener::class);
    }
}
