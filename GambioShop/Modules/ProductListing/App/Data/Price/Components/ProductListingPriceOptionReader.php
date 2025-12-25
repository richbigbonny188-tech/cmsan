<?php
/* --------------------------------------------------------------
   ProductListingPriceOptionReader.php 2023-06-14
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
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CheapestPriceNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingPriceOptionReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingPriceOptionReader
{
    private Connection $connection;
    
    
    /**
     * ProductListingPriceOptionReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Fetches the cheapest option price for the given listing item id.
     *
     *
     * @param ListingItemId $id
     *
     * @return float
     */
    public function fetchCheapestOptionId(ListingItemId $id): float
    {
        $qb    = $this->connection->createQueryBuilder();
        $where = $qb->expr()->eq('products_id', $qb->createNamedParameter($id->asInt()));
        $statement = $qb->select('options_values_price')
            ->from('products_attributes')
            ->where($where)
            ->setMaxResults(1)
            ->orderBy('options_values_price')
            ->executeQuery();
        $result    = $statement->fetchAssociative();
        if (false === $result || !array_key_exists('options_values_price', $result)) {
            return 0.0;
        }
        return (float)$result['options_values_price'];
    }
}