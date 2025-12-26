<?php
/*--------------------------------------------------------------
   ShoppingCartWriteService.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartFactory;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartRepository;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartWriteService as ShoppingCartWriteServiceInterface;

class ShoppingCartWriteService implements ShoppingCartWriteServiceInterface
{
    /**
     * @var ShoppingCartFactory
     */
    private ShoppingCartFactory $factory;
    
    /**
     * @var ShoppingCartRepository
     */
    private ShoppingCartRepository $repository;
    
    
    /**
     * Constructor.
     */
    public function __construct(ShoppingCartFactory $factory, ShoppingCartRepository $repository)
    {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * Removes all Items from the Shopping Cart for a given Customer ID
     */
    public function removeShoppingCart(int $customerId): void
    {
        $this->repository->removeShoppingCart($this->factory->createCustomerId($customerId));
    }
}