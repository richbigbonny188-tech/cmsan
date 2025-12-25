<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Attributes\Representation\Id\Listener\OnPresentSellingUnitIdEventListener;
use Gambio\Shop\Attributes\Representation\Id\Repository\Factories\PresentationIdFactory;
use Gambio\Shop\Attributes\Representation\Id\Repository\Factories\PresentationIdFactoryInterface;
use Gambio\Shop\Attributes\Representation\Id\Repository\Readers\Reader;
use Gambio\Shop\Attributes\Representation\Id\Repository\Readers\ReaderInterface;
use Gambio\Shop\Attributes\Representation\Id\Repository\Repository;
use Gambio\Shop\Attributes\Representation\Id\Repository\RepositoryInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers\CombisIdIdentifier;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers\CombisIdIdentifierInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\OnPresentSellingUnitIdEvent;
use PropertiesDataAgent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Attributes\Representation\Id
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
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ReadServiceInterface::class, ReadService::class)
            ->addArgument(RepositoryInterface::class);
        
        $this->application->registerShared(RepositoryInterface::class, Repository::class)
            ->addArgument(ReaderInterface::class)
            ->addArgument(PresentationIdFactoryInterface::class)
            ->addArgument(CombisIdIdentifierInterface::class);
        
        $this->application->registerShared(ReaderInterface::class, Reader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(PresentationIdFactoryInterface::class, PresentationIdFactory::class);
        
        $this->application->registerShared(CombisIdIdentifierInterface::class, CombisIdIdentifier::class)
            ->addArgument(PropertiesDataAgent::class);
        
        $this->application->registerShared(PropertiesDataAgent::class);
        
        $this->application->registerShared(OnPresentSellingUnitIdEventListener::class)
            ->addArgument(ReadServiceInterface::class);
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