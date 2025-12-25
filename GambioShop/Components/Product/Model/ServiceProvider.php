<?php
/*--------------------------------------------------------------------
 ServiceProvider.php 2022-04-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Model;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Event\EventListenerProvider;
use Gambio\Shop\Product\Model\Criteria\ShowModelCriteria;
use Gambio\Shop\Product\Model\Listener\OnGetSellingUnitModelEventListener;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetSellingUnitModelEvent;

/**
 * Class ServiceProvider
 *
 * @package Gambio\Shop\Product\Model
 */
class ServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var string[]
     */
    public function provides(): array
    {
        return [
            OnGetSellingUnitModelEventListener::class,
            ShowModelCriteria::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $configuration = $this->application->get(ConfigurationService::class);
        $showInShoppingCartAndWishlist = $configuration->find('gm_configuration/SHOW_PRODUCTS_MODEL_IN_SHOPPING_CART_AND_WISHLIST');
        $showInProductLists = $configuration->find('gm_configuration/SHOW_PRODUCTS_MODEL_IN_PRODUCT_LISTS');
        $showInProductDetails = $configuration->find('gm_configuration/SHOW_PRODUCTS_MODEL_IN_PRODUCT_DETAILS');
        
        $this->application->registerShared(ShowModelCriteria::class)
            ->addArgument($showInShoppingCartAndWishlist && $showInShoppingCartAndWishlist->value() === 'true')
            ->addArgument($showInProductLists && $showInProductLists->value() === 'true')
            ->addArgument($showInProductDetails && $showInProductDetails->value() === 'true');
        
        $this->application->registerShared(OnGetSellingUnitModelEventListener::class)
            ->addArgument(ShowModelCriteria::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        /** @var EventListenerProvider $listenerProvider */
        $listenerProvider = $this->application->get(EventListenerProvider::class);
        $listenerProvider->attachListener(OnGetSellingUnitModelEvent::class, OnGetSellingUnitModelEventListener::class);
    }
}