<?php
/* --------------------------------------------------------------
   ProductListingPricePersonalOfferRepository.php 2023-06-14
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
use Exception;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemGroupSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingPricePersonalOfferRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPricePersonalOfferRepository
{
    private const COLUMN_PRODUCT_ID     = 'products_id';
    private const COLUMN_QUANTITY       = 'quantity';
    private const COLUMN_QUANTITY_EQ    = '1.0000';
    private const COLUMN_PERSONAL_OFFER = 'personal_offer';
    
    private Connection $connection;
    
    
    /**
     * ProductListingPricePersonalOfferRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Searches for a personal offer of the listing item.
     *
     * Searches only for personal offers with quantity of "1.0000", because we are in a
     * product listing context with a fixed quantity of 1.
     *
     * @param ListingItemId            $itemId
     * @param ListingItemGroupSettings $groupSettings
     *
     * @return float|null
     */
    public function findPersonalOffer(ListingItemId $itemId, ListingItemGroupSettings $groupSettings): ?float
    {
        $table = "personal_offers_by_customers_status_{$groupSettings->groupId()}";
        $qb    = $this->connection->createQueryBuilder();
        
        $productIdEq   = $qb->expr()->eq(self::COLUMN_PRODUCT_ID, $itemId->asInt());
        $quantityIsOne = $qb->expr()->eq(self::COLUMN_QUANTITY, self::COLUMN_QUANTITY_EQ);
        $where         = $qb->expr()->and($productIdEq, $quantityIsOne);
        
        try {
            $statement = $qb->select(self::COLUMN_PERSONAL_OFFER)->from($table)->where($where)->executeQuery();
        } catch (Exception $exception) {
            return null;
        }
        $result = $statement->fetchAssociative();
        if (!$this->hasPersonalOffer($result)) {
            return null;
        }
        
        $offer = (float)$result[self::COLUMN_PERSONAL_OFFER];
        if ($offer <= 0.0) {
            return null;
        }
        
        return $offer;
    }
    
    
    /**
     * Checks if $haystack is an array containing the key 'personal_offer'.
     *
     * @param $haystack
     *
     * @return bool
     */
    private function hasPersonalOffer($haystack): bool
    {
        return is_array($haystack) && array_key_exists(self::COLUMN_PERSONAL_OFFER, $haystack);
    }
}
