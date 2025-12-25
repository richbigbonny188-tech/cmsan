<?php
/* --------------------------------------------------------------
  ServiceProvider.php 2020-10-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\LegalAgeFlag;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\LegalAgeFlag\Listener\OnGetProductInfoEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductInfoEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\LegalAgeFlag
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetProductInfoEventListener::class
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
        $listenerProvider->attachListener(OnGetProductInfoEvent::class, OnGetProductInfoEventListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetProductInfoEventListener::class);
    }
}