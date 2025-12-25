<?php
/*--------------------------------------------------------------
   CustomerCartRepository.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data\CustomerCartMapper;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data\ShoppingCartReader;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data\ShoppingCartWriter;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ShoppingCart;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartRepository;

class CustomerCartRepository implements ShoppingCartRepository
{
    /**
     * @var ShoppingCartReader
     */
    private ShoppingCartReader $reader;
    
    /**
     * @var ShoppingCartWriter
     */
    private ShoppingCartWriter $writer;
    
    /**
     * @var CustomerCartMapper
     */
    private CustomerCartMapper $mapper;
    
    
    /**
     * Constructor.
     */
    public function __construct(ShoppingCartReader $reader, ShoppingCartWriter $writer, $mapper)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns a Shopping Cart instance for a given Customer ID
     */
    public function getShoppingCart(CustomerId $customerId, int $languageId): ShoppingCart
    {
        $cartData = $this->reader->getShoppingCart($customerId, $languageId);
        
        return $this->mapper->mapShoppingCart($cartData, $customerId);
    }
    
    
    /**
     * Removed the Shopping Cart for a given Customer ID
     */
    public function removeShoppingCart(CustomerId $customerId): void
    {
        $this->writer->deleteCart($customerId);
    }
}