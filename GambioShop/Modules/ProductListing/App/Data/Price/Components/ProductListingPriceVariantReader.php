<?php
/* --------------------------------------------------------------
   ProductListingPriceVariantReader.php 2023-06-14
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
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CheapestPriceNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingPriceVariantReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingPriceVariantReader
{
    private const VARIANTS_TABLE             = 'products_properties_combis';
    private const VARIANTS_ORDER             = 'combi_price, products_properties_combis_id';
    private const VARIANTS_COLUMN_PRODUCT_ID = 'products_id';
    private const VARIANTS_COLUMN_PRICE      = 'combi_price';
    private const VARIANTS_COLUMN_QUANTITY   = 'combi_quantity';
    
    private Connection $connection;
    
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    public function fetchCheapestVariantPriceWithoutStockCheck(ListingItemId $id): float
    {
        $qb    = $this->connection->createQueryBuilder();
        $where = $qb->expr()->eq(self::VARIANTS_COLUMN_PRODUCT_ID, $qb->createNamedParameter($id->asInt()));
        
        $statement = $this->cheapestVariantBaseQuery($qb)->where($where)->executeQuery();
        $result    = $statement->fetchAssociative();
        if (false === $result || !$this->combiPriceExists($result)) {
            $message = "Cheapest variant for product with id '{$id->asInt()}' and without stock check not found.";
            throw new CheapestPriceNotFoundException($message);
        }
        
        return (float)$result[self::VARIANTS_COLUMN_PRICE];
    }
    
    
    public function fetchCheapestVariantPriceWithStock(ListingItemId $id): float
    {
        $qb          = $this->connection->createQueryBuilder();
        $pIdEq       = $qb->expr()->eq(self::VARIANTS_COLUMN_PRODUCT_ID, $qb->createNamedParameter($id->asInt()));
        $combiGtZero = $qb->expr()->gt(self::VARIANTS_COLUMN_QUANTITY, 0);
        $where       = $qb->expr()->and($pIdEq, $combiGtZero);
        
        $statement = $this->cheapestVariantBaseQuery($qb)->where($where)->executeQuery();
        $result    = $statement->fetchAssociative();
        if (false === $result || !$this->combiPriceExists($result)) {
            $message = "Cheapest variant for product with id '{$id->asInt()}' and stock available not found.";
            throw new CheapestPriceNotFoundException($message);
        }
        
        return (float)$result[self::VARIANTS_COLUMN_PRICE];
    }
    
    
    private function cheapestVariantBaseQuery(QueryBuilder $qb): QueryBuilder
    {
        return $qb->select(self::VARIANTS_COLUMN_PRICE)
            ->from(self::VARIANTS_TABLE)
            ->setMaxResults(1)
            ->orderBy(self::VARIANTS_ORDER);
    }
    
    
    private function combiPriceExists($haystack): bool
    {
        return is_array($haystack) && array_key_exists('combi_price', $haystack);
    }
}