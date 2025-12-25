<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2020-2-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\TaxInfo;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\TaxInfo\Listener\OnGetSellingUnitTaxInfoEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitTaxInfoEvent;
use main_ORIGIN;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\TaxInfo
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitTaxInfoEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitTaxInfoEventListener::class)
            ->addArgument(main_ORIGIN::class);
        
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
        $listenerProvider->attachListener(OnGetSellingUnitTaxInfoEvent::class,
                                          OnGetSellingUnitTaxInfoEventListener::class);
    }
}