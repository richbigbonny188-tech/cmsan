<?php
/**
 * ServiceProvider.php 2020-10-19
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\GxCustomizer\Representation\Id;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\GxCustomizer\Representation\Id\Factories\CustomizerPresentationIdFactory;
use Gambio\Shop\GxCustomizer\Representation\Id\Listener\OnPresentSellingUnitIdEventListener;
use Gambio\Shop\SellingUnit\Presentation\Events\OnPresentSellingUnitIdEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\GxCustomizer\Representation\Id
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnPresentSellingUnitIdEventListener::class
        ];
    }
    
    
    public function register(): void
    {
        $this->application->registerShared(CustomizerPresentationIdFactory::class);
        $this->application->registerShared(OnPresentSellingUnitIdEventListener::class)
            ->addArgument(CustomizerPresentationIdFactory::class);
    }
    
    
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnPresentSellingUnitIdEvent::class,
                                          OnPresentSellingUnitIdEventListener::class);
    }
}