<?php
/* --------------------------------------------------------------
   ProductListingPriceVariantCheckReader.php 2023-06-14
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
use Doctrine\DBAL\Exception as DBALException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingPriceVariantCheckReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingPriceVariantCheckReader
{
    private Connection $connection;
    
    
    /**
     * ProductListingPriceVariantCheckReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Checks if listing item has product variant which changes the price.
     *
     * @param ListingItemId $itemId
     *
     * @return bool
     */
    public function hasPriceChangingProductVariant(ListingItemId $itemId): bool
    {
        $query = <<<SQL
SELECT COUNT(*) AS count
FROM products_properties_combis
WHERE products_id = '{$itemId->asInt()}'
GROUP BY combi_price;
SQL;
        try {
            $statement = $this->connection->executeQuery($query);
        } catch (DBALException $e) {
            return false;
        }
        $rowCount = $statement->rowCount();
        
        return $rowCount > 1;
    }
    
    
    /**
     * Checks if listing item has product option which changes the price.
     *
     * @param ListingItemId $itemId
     *
     * @return bool
     */
    public function hasPriceChangingProductOption(ListingItemId $itemId): bool
    {
        $qb = $this->connection->createQueryBuilder();
        
        $pIdEq            = $qb->expr()->eq('pa.products_id', $qb->createNamedParameter($itemId->asInt()));
        $priceGreaterZero = $qb->expr()->gt('pa.options_values_price', 0);
        $where            = $qb->expr()->and($pIdEq, $priceGreaterZero);
        
        $joinCondition = 'po.products_options_id = pa.options_id';
        $statement     = $qb->select('COUNT(*) as total')
            ->from('products_options', 'po')
            ->leftJoin('po', 'products_attributes', 'pa', $joinCondition)
            ->where($where)
            ->executeQuery();
        $result        = $statement->fetchAssociative();
        if (!$this->hasTotal($result)) {
            return false;
        }
        $total = $result['total'] ?? 0;
        
        return $total > 0;
    }
    
    
    /**
     * Checks if $result is of type array and contains the key 'total'.
     *
     * @param $result
     *
     * @return bool
     */
    private function hasTotal($result): bool
    {
        return is_array($result) && array_key_exists('total', $result);
    }
}
