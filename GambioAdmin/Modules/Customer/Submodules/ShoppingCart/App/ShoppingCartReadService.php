<?php
/*--------------------------------------------------------------
   ShoppingCartReadService.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ShoppingCart;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartFactory;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartReadService as ShoppingCartReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartRepository;

class ShoppingCartReadService implements ShoppingCartReadServiceInterface
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
     * Get a Shopping Cart instance for a given Customer ID
     */
    public function getShoppingCart(int $customerId, int $languageId): ShoppingCart
    {
        return $this->repository->getShoppingCart($this->factory->createCustomerId($customerId), $languageId);
    }
}