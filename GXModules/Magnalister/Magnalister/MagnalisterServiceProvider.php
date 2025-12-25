<?php
/* --------------------------------------------------------------
   MagnalisterServiceProvider.php 2021-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Magnalister\Magnalister;

use Gambio\Admin\Layout\Menu\Events\CoreMenuDataCollected;
use Gambio\Admin\Layout\Menu\Factories\CacheMenuFactory;
use Gambio\Core\Application\DependencyInjection\AbstractModuleBootableServiceProvider;
use GXModules\Magnalister\Magnalister\Admin\EventListener\MagnalisterMenuExtender;

/**
 * Class MagnalisterServiceProvider
 *
 * @package GXModules\Magnalister\Magnalister
 */
class MagnalisterServiceProvider extends AbstractModuleBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            MagnalisterMenuExtender::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->attachEventListener(CoreMenuDataCollected::class, MagnalisterMenuExtender::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(MagnalisterMenuExtender::class)->addArgument(CacheMenuFactory::class);
    }
}
