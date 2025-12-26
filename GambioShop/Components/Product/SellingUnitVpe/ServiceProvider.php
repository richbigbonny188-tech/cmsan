<?php
/*--------------------------------------------------------------------------------------------------
    ServiceProvider.php 2020-3-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/*--------------------------------------------------------------------
 ServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitVpe;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\SellingUnitVpe\Listeners\OnGetSellingUnitVpeEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitVpeEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\SellingUnitVpe
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitVpeEventListener::class
        ];
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
        $listenerProvider->attachListener(OnGetSellingUnitVpeEvent::class, OnGetSellingUnitVpeEventListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitVpeEventListener::class);
    }
}