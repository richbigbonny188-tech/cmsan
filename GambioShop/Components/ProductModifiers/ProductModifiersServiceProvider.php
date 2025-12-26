<?php
/*--------------------------------------------------------------------------------------------------
    ProductModifiersServiceProvider.php 2020-10-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

namespace Gambio\Shop\ProductModifiers;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTOBuilder;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTOBuilderInterface;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTOBuilder;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTOBuilderInterface;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;
use Gambio\Shop\ProductModifiers\Database\Presentation\PresentationMapperFactory;

/**
 * Class ProductModifiersServiceProvider
 *
 * @package Gambio\Shop\ProductModifiers
 */
class ProductModifiersServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            PresentationMapperInterface::class,
            GroupDTOBuilderInterface::class,
            ModifierDTOBuilderInterface::class
        ];
    }
    
    
    public function boot(): void
    {
        // TODO: Implement boot() method.
    }
    
    
    /**
     *
     */
    public function register(): void
    {
        $factory = new PresentationMapperFactory();
        $mappers = $factory->createMapperChain();
        $this->application->registerShared(PresentationMapperInterface::class, $mappers);
        $this->application->registerShared(GroupDTOBuilderInterface::class, GroupDTOBuilder::class);
        $this->application->registerShared(ModifierDTOBuilderInterface::class, ModifierDTOBuilder::class);
    }
}