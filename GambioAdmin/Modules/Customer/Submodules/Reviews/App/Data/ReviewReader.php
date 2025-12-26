<?php
/*--------------------------------------------------------------
   ReviewReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\ReviewId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\Exceptions\ReviewDoesNotExistException;

/**
 * Class ReviewReader
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data
 */
class ReviewReader
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Returns all available customer reviews.
     *
     * @param CustomerId $customerId
     *
     * @return array
     * @throws Exception
     */
    public function getCustomerReviews(CustomerId $customerId): array
    {
        return $this->createQuery()
            ->where('r.customers_id=:customers_id')
            ->setParameter('customers_id', $customerId->value())
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * Returns a specific customer review based on the given ID.
     *
     * @param ReviewId $reviewId
     *
     * @return array
     * @throws ReviewDoesNotExistException
     * @throws Exception
     */
    public function getCustomerReviewByReviewId(ReviewId $reviewId): array
    {
        $result = $this->createQuery()
            ->where('r.reviews_id=:reviews_id')
            ->setParameter('reviews_id', $reviewId->value())
            ->executeQuery();
        
        if ($result->rowCount() !== 1) {
            throw ReviewDoesNotExistException::forReviewId($reviewId);
        }
        
        return $result->fetchAllAssociative();
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function createQuery(): QueryBuilder
    {
        $columns = [
            'r.reviews_id',
            'r.customers_id',
            'r.products_id',
            'l.code',
            'rd.reviews_text',
            'r.date_added',
            'r.reviews_rating',
        ];
        
        return $this->connection->createQueryBuilder()
            ->select(implode(', ', $columns))
            ->from('reviews', 'r')
            ->innerJoin('r', 'reviews_description', 'rd', 'rd.reviews_id=r.reviews_id')
            ->innerJoin('rd', 'languages', 'l', 'l.languages_id=rd.languages_id')
            ->groupBy(implode(', ', $columns))
            ->orderBy('r.date_added', 'DESC');
    }
}