<?php
/*--------------------------------------------------------------------------------------------------
    ServiceProvider.php 2020-11-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\SelectionHtml;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Properties\Representation\SelectionHtml\Listener\OnGetModifierHtmlEventListener;
use Gambio\Shop\Properties\Representation\SelectionHtml\Repository\Readers\Reader;
use Gambio\Shop\Properties\Representation\SelectionHtml\Repository\Readers\ReaderInterface;
use Gambio\Shop\Properties\Representation\SelectionHtml\Repository\Repository;
use Gambio\Shop\Properties\Representation\SelectionHtml\Repository\RepositoryInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\OnGetModifierHtmlEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Properties\Representation\SelectionHtml
 * @codeCoverageIgnore service providers don't need to be tested
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    public function provides(): array
    {
        return [
            OnGetModifierHtmlEventListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnGetModifierHtmlEventListener::class)
            ->addArgument(ReadServiceInterface::class);
        $this->application->registerShared(ReadServiceInterface::class, ReadService::class)
            ->addArgument(RepositoryInterface::class);
        $this->application->registerShared(RepositoryInterface::class, Repository::class)
            ->addArgument(ReaderInterface::class);
        $this->application->registerShared(ReaderInterface::class, Reader::class)->addArgument(Connection::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnGetModifierHtmlEvent::class, OnGetModifierHtmlEventListener::class);
    }
}
