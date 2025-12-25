<?php
/*--------------------------------------------------------------------------------------------------
    ServiceProvider.php 2020-11-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Properties\SellingUnit\Database;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Properties\Database\Services\Interfaces\PropertiesReaderServiceInterface;
use Gambio\Shop\Properties\SellingUnit\Database\Listener\OnSellingUnitIdCreateListener;
use Gambio\Shop\SellingUnit\Unit\Events\OnSellingUnitIdCreateEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Properties\SellingUnit\Database
 * @codeCoverageIgnore service providers don't need to be tested
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnSellingUnitIdCreateListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnSellingUnitIdCreateEvent::class, OnSellingUnitIdCreateListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnSellingUnitIdCreateListener::class)
            ->addArgument(PropertiesReaderServiceInterface::class);
    }
}
