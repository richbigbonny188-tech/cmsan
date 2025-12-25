<?php
/*------------------------------------------------------------------------------
 ServiceProvider.php 2020-03-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Ean;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\PrioritizingEventListenerProvider;
use Gambio\Shop\Product\Ean\Listener\OnGetSellingUnitEanEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitEanEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\Ean
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitEanEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /**
         * @var PrioritizingEventListenerProvider $prioritizingListenerProvider
         */
        $prioritizingListenerProvider = $this->application->get(PrioritizingEventListenerProvider::class);
        $prioritizingListenerProvider->attachListener(OnGetSellingUnitEanEvent::class,
                                                      OnGetSellingUnitEanEventListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitEanEventListener::class);
    }
}