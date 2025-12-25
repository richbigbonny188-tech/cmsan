<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2021-03-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\Shop\Attributes\ProductModifiers\Database;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Attributes\ProductModifiers\Database\Builders\AttributeGroupBuilder;
use Gambio\Shop\Attributes\ProductModifiers\Database\Builders\AttributeModifierBuilder;
use Gambio\Shop\Attributes\ProductModifiers\Database\Listeners\OnCreateGroupsForSellingUnitListener;
use Gambio\Shop\Attributes\ProductModifiers\Database\Listeners\OnModifierIdCreateListener;
use Gambio\Shop\Attributes\ProductModifiers\Database\Readers\AttributeGroupReader;
use Gambio\Shop\Attributes\ProductModifiers\Database\Readers\AttributeGroupReaderInterface;
use Gambio\Shop\Attributes\ProductModifiers\Database\Readers\AttributeModifierReader;
use Gambio\Shop\Attributes\ProductModifiers\Database\Readers\AttributeModifierReaderInterface;
use Gambio\Shop\Attributes\ProductModifiers\Database\Repository\AttributeGroupRepository;
use Gambio\Shop\Attributes\ProductModifiers\Database\Repository\AttributeGroupRepositoryInterface;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTOBuilderInterface;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTOBuilderInterface;
use Gambio\Shop\ProductModifiers\Database\Core\Events\OnCreateGroupsForSellingUnitEvent;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;
use Gambio\Shop\ProductModifiers\Modifiers\Events\OnModifierIdCreateEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Attributes\ProductModifiers\Database
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [
            OnModifierIdCreateListener::class,
            OnCreateGroupsForSellingUnitListener::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnModifierIdCreateEvent::class, OnModifierIdCreateListener::class);
        $listenerProvider->attachListener(OnCreateGroupsForSellingUnitEvent::class,
                                          OnCreateGroupsForSellingUnitListener::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(OnModifierIdCreateListener::class);
        
        $stockCheck              = defined('STOCK_CHECK') ? STOCK_CHECK === 'true' : false;
        $attributeStockCheck     = defined('ATTRIBUTE_STOCK_CHECK') ? ATTRIBUTE_STOCK_CHECK === 'true' : false;
        $stockAllowCheckout      = defined('STOCK_ALLOW_CHECKOUT') ? STOCK_ALLOW_CHECKOUT === 'true' : false;
        $setAttributesOutOfStock = defined('GM_SET_OUT_OF_STOCK_ATTRIBUTES') ? GM_SET_OUT_OF_STOCK_ATTRIBUTES
                                                                               === 'true' : false;
        $showAttributesStock     = defined('GM_SET_OUT_OF_STOCK_ATTRIBUTES_SHOW') ? GM_SET_OUT_OF_STOCK_ATTRIBUTES_SHOW
                                                                                    === 'true' : false;
        
        $checkStockBeforeShoppingCart = defined('CHECK_STOCK_BEFORE_SHOPPING_CART') ? CHECK_STOCK_BEFORE_SHOPPING_CART
                                                                                      === 'true' : false;
        
        $this->application->registerShared(AttributeModifierReaderInterface::class, AttributeModifierReader::class)
            ->addArgument(Connection::class)
            ->addArgument(ModifierDTOBuilderInterface::class)
            ->addArgument($stockCheck)
            ->addArgument($attributeStockCheck)
            ->addArgument($stockAllowCheckout)
            ->addArgument($setAttributesOutOfStock)
                        ->addArgument($checkStockBeforeShoppingCart)
                        ->addArgument($showAttributesStock);
        
        $this->application->registerShared(AttributeGroupBuilder::class);
        $this->application->registerShared(AttributeModifierBuilder::class);
        
        $this->application->registerShared(OnCreateGroupsForSellingUnitListener::class)
            ->addArgument(AttributeGroupRepositoryInterface::class);
        
        $this->application->registerShared(AttributeGroupReaderInterface::class, AttributeGroupReader::class)
            ->addArgument(Connection::class)
            ->addArgument(GroupDTOBuilderInterface::class);
        
        $this->application->registerShared(AttributeGroupRepositoryInterface::class, AttributeGroupRepository::class)
            ->addArgument(AttributeGroupReaderInterface::class)
            ->addArgument(AttributeModifierReaderInterface::class)
            ->addArgument(AttributeGroupBuilder::class)
            ->addArgument(AttributeModifierBuilder::class)
            ->addArgument(PresentationMapperInterface::class);
    }
}
