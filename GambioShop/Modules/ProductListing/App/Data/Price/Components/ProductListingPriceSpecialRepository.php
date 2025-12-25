<?php
/* --------------------------------------------------------------
   ProductListingPriceSpecialRepository.php 2023-12-05
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
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingPriceSpecialRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingPriceSpecialRepository
{
    private const COLUMN_PRICE = 'specials_new_products_price';
    
    private const COLUMN_PRODUCT_ID = 'products_id';
    
    private const COLUMN_STATUS = 'status';
    
    private const TABLE_SPECIALS = 'specials';
    
    private const CONFIGURATION_KEY_SHOW_OLD_SPECIAL_PRICE = 'gm_configuration/SHOW_OLD_SPECIAL_PRICE';
    
    
    /**
     * ProductListingPriceSpecialRepository constructor.
     *
     * @param Connection          $connection
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(private Connection $connection, private ConfigurationFinder $configurationFinder)
    {
    }
    
    
    /**
     * Searches for special price of given product.
     * Return NULL if there is no special associated with the given listing item id.
     *
     * @param ListingItemId $id
     *
     * @return float|null
     */
    public function findSpecialPrice(ListingItemId $id): ?float
    {
        $qb            = $this->connection->createQueryBuilder();
        $pIdEq         = $qb->expr()->eq(self::COLUMN_PRODUCT_ID, $qb->createNamedParameter($id->asInt()));
        $statusEnabled = $qb->expr()->eq(self::COLUMN_STATUS, 1);
        
        $where     = $qb->expr()->and($pIdEq, $statusEnabled);
        $statement = $qb->select(self::COLUMN_PRICE)->from(self::TABLE_SPECIALS)->where($where)->executeQuery();
        $result    = $statement->fetchAssociative();
        if (false === $result || !$this->priceExists($result)) {
            return null;
        }
        
        return (float)$result[self::COLUMN_PRICE];
    }
    
    
    /**
     * @param ListingItemId $itemId
     *
     * @return bool
     */
    public function hasSpecial(ListingItemId $itemId): bool
    {
        return $this->findSpecialPrice($itemId) !== null;
    }
    
    
    /**
     * Returns whether the `SHOW_OLD_SPECIAL_PRICE` configuration is active or not
     *
     * @return bool
     */
    public function showOldSpecialPrice(): bool
    {
        $value = $this->configurationFinder->get(self::CONFIGURATION_KEY_SHOW_OLD_SPECIAL_PRICE);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    
    
    /**
     * Checks if $haystack is an array and contains and entry for the specials price column.
     *
     * @param $haystack
     *
     * @return bool
     */
    private function priceExists($haystack): bool
    {
        return is_array($haystack) && array_key_exists(self::COLUMN_PRICE, $haystack);
    }
}