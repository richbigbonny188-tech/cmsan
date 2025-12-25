<?php
/*--------------------------------------------------------------------------------------------------
    ServiceProvider.php 2020-10-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\GxCustomizer\ProductModifiers\Database;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\GxCustomizer\ProductModifiers\Database\Listeners\OnModifierIdCreateListener;
use Gambio\Shop\ProductModifiers\Modifiers\Events\OnModifierIdCreateEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\GxCustomizer\ProductModifiers\Database
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnModifierIdCreateListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnModifierIdCreateListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnModifierIdCreateEvent::class,
                                          OnModifierIdCreateListener::class);
    }
    
    
}