<?php
/*--------------------------------------------------------------
   ShoppingCartWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;
use Gambio\Core\Cache\Services\CacheFactory;

class ShoppingCartWriter
{
    /**
     * @var Connection
     */
    private Connection $database;
    
    /**
     * @var CacheFactory
     */
    private CacheFactory $factory;
    
    
    /**
     * Constructor.
     */
    public function __construct(Connection $database, CacheFactory $factory)
    {
        $this->database = $database;
        $this->factory  = $factory;
    }
    
    
    /**
     * Removes the Cart items for a given Customer ID from the Database.
     *
     * @throws Exception
     */
    public function deleteCart(CustomerId $customerId): void
    {
        $this->database->createQueryBuilder()
            ->delete('customers_basket')
            ->where('customers_id = :customer_id')
            ->setParameter("customer_id", $customerId->value())
            ->executeQuery();
        
        $this->factory->createCacheFor("shopping_carts_to_reset")->set((string)$customerId->value(), true);
    }
}