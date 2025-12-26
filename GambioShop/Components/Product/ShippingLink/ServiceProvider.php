<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2020-11-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\ShippingLink;

use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\ShippingLink\Listener\OnGetShippingInfoEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetShippingInfoEvent;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use main_ORIGIN;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\ShippingLink
 * @codeCoverageIgnore
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetShippingInfoEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetShippingInfoEventListener::class);
        
        $this->application->registerShared(main_ORIGIN::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /**
         * @var EventListenerProvider $listenerProvider
         */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnGetShippingInfoEvent::class, OnGetShippingInfoEventListener::class);
    }
}
