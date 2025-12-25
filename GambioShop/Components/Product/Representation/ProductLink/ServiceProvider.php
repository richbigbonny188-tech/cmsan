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

namespace Gambio\Shop\Product\Representation\ProductLink;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\Representation\ProductLink\Listener\OnGetSellingUnitProductLinkEventListener;
use Gambio\Shop\SellingUnit\Presentation\Events\OnGetSellingUnitProductLinkEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\Representation\ProductLink
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitProductLinkEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetSellingUnitProductLinkEventListener::class);
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
        $listenerProvider->attachListener(OnGetSellingUnitProductLinkEvent::class,
                                          OnGetSellingUnitProductLinkEventListener::class);
    }
}