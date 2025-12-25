<?php
/*--------------------------------------------------------------
   WishlistWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\CustomerId;
use Gambio\Core\Cache\Services\CacheFactory;

class WishlistWriter
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
     * Constructor
     */
    public function __construct(Connection $connection, CacheFactory $factory)
    {
        $this->database = $connection;
        $this->factory  = $factory;
    }
    
    
    /**
     * Deletes the Wishlist for a given user. Also adds a cache to reset the wishlist session.
     *
     * @throws Exception
     */
    public function deleteWishlist(CustomerId $customerId): void
    {
        $this->database->createQueryBuilder()
            ->delete('customers_wishlist')
            ->where('customers_id = :customer_id')
            ->setParameter("customer_id", $customerId->value())
            ->executeQuery();
        
        $this->factory->createCacheFor("wishlists_to_reset")->set((string)$customerId->value(), true);
    }
    
}