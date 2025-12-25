<?php
/* --------------------------------------------------------------
   ProductListingPriceDiscountRepository.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Doctrine\DBAL\Connection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemGroupSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingPriceDiscountRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceDiscountRepository
{
    private const TABLE             = 'products';
    private const COLUMN_PRODUCT_ID = 'products_id';
    private const COLUMN_DISCOUNT   = 'products_discount_allowed';
    
    private Connection $connection;
    
    
    /**
     * ProductListingPriceDiscountRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Searches for a products discount value.
     *
     * It is required for the group settings discount to be greater than zero, otherwise null is returned.
     * The group discount limits the final product discount, so if the products discount is higher than
     * the group discount, the group discount value is returned.
     *
     * @param ListingItemId            $itemId
     * @param ListingItemGroupSettings $groupSettings
     *
     * @return float|null
     */
    public function findDiscount(ListingItemId $itemId, ListingItemGroupSettings $groupSettings): ?float
    {
        $groupDiscount = $groupSettings->groupDiscount();
        if ($groupDiscount <= 0.0) {
            return null;
        }
        
        if ($discount = $this->fetchDiscount($itemId)) {
            return min($groupDiscount, $discount);
        }
        
        return null;
    }
    
    
    /**
     * Fetches the products discount value.
     *
     * The method will return null in two cases. The first one is if there is no product
     * associated with the given id. The second case is if the products discount us zero.
     *
     * @param ListingItemId $itemId
     *
     * @return float|null
     */
    private function fetchDiscount(ListingItemId $itemId): ?float
    {
        $qb = $this->connection->createQueryBuilder();
        
        $where     = $qb->expr()->eq(self::COLUMN_PRODUCT_ID, $qb->createNamedParameter($itemId->asInt()));
        $statement = $qb->select(self::COLUMN_DISCOUNT)->from(self::TABLE)->where($where)->executeQuery();
        
        $result = $statement->fetchAssociative();
        if (false === $result) {
            return null;
        }
        
        $result = (float)$result[self::COLUMN_DISCOUNT];
        if ($result <= 0.0) {
            return null;
        }
        
        return $result;
    }
}