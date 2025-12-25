<?php
/* --------------------------------------------------------------
   ViewedProductsReader.php 2023-09-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Exceptions\UnexpectedViewedProductsDbException;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\Pagination;

/**
 * Class ViewedProductsReader
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data
 */
class ViewedProductsReader
{
    /**
     * ViewedProductsReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection)
    {
    }
    
    
    /**
     * Fetches viewed products datasets.
     * Throws an exception in case of a database error.
     *
     * @param Pagination $pagination
     *
     * @return array
     * @throws UnexpectedViewedProductsDbException
     */
    public function fetchData(Pagination $pagination): array
    {
        $qb = $this->connection->createQueryBuilder();
        
        try {
            $statement = $qb->select('pd.products_id as id, pd.products_name as name, l.name as language, pd.products_viewed as views')
                ->from('products_description', 'pd')
                ->leftJoin('pd', 'languages', 'l', 'pd.language_id = l.languages_id')
                ->where('pd.products_viewed != 0')
                ->orderBy('views', 'desc')
                ->setFirstResult($pagination->mysqlOffset())
                ->setMaxResults($pagination->itemsPerPage())
                ->executeQuery();
        } catch (Exception $e) {
            $message = "Failed to fetch viewed products data due to database error: {$e->getMessage()}";
            throw new UnexpectedViewedProductsDbException($message, previous: $e);
        }
        
        return $statement->fetchAllAssociative();
    }
    
    
    /**
     * Fetches count of total datasets for viewed products data.
     * Throws an exception in case of a database error or if no data was found.
     *
     * @return int
     * @throws UnexpectedViewedProductsDbException
     */
    public function fetchTotal(): int
    {
        $qb = $this->connection->createQueryBuilder();
        try {
            $statement = $qb->select('COUNT(*) as count')
                ->from('products_description', 'pd')
                ->where('pd.products_viewed != 0')
                ->executeQuery();
        } catch (Exception $e) {
            $message = "Failed to fetch viewed products total due to database error: {$e->getMessage()}";
            throw new UnexpectedViewedProductsDbException($message, previous: $e);
        }
        
        $result = $statement->fetchAssociative();
        if ($result === false) {
            $message = 'Failed to fetch viewed products total';
            throw new UnexpectedViewedProductsDbException($message);
        }
        
        return (int)$result['count'];
    }
}