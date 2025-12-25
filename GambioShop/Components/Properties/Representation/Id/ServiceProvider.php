<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2020-11-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\Id;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Properties\Database\Services\Interfaces\PropertiesReaderServiceInterface;
use Gambio\Shop\Properties\Representation\Id\Listener\OnPresentSellingUnitIdEventListener;
use Gambio\Shop\SellingUnit\Presentation\Events\OnPresentSellingUnitIdEvent;

/**
 * Class ServiceProvider
 * @package Gambio\Shop\SellingUnit\Properties\Representation\Id
 * @codeCoverageIgnore service providers don't need to be tested
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    public function provides(): array
    {
        return [
            OnPresentSellingUnitIdEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnPresentSellingUnitIdEventListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class);
        
        //$this->application->registerShared(PropertiesReaderServiceInterface::class, PropertiesReaderService::class);
        //$this->application->registerShared()
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnPresentSellingUnitIdEvent::class,
                                          OnPresentSellingUnitIdEventListener::class);
    }
}
