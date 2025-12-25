<?php
/* --------------------------------------------------------------
   TrackingCodeReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\TrackingCodeNotFoundException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class TrackingCodeReader
 *
 * @package Gambio\Admin\Modules\TrackingCode\App\Data
 */
class TrackingCodeReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * TrackingCodeReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param TrackingCodeId $id
     *
     * @return array
     *
     * @throws TrackingCodeNotFoundException
     * @throws Exception
     */
    public function getTrackingCodeDataById(TrackingCodeId $id): array
    {
        $trackingCodeData = $this->db->createQueryBuilder()
            ->select('orders_parcel_tracking_codes.*, languages.code as `language_code`')
            ->from('orders_parcel_tracking_codes')
            ->join('orders_parcel_tracking_codes',
                   'languages',
                   'languages',
                   'orders_parcel_tracking_codes.language_id = languages.languages_id')
            ->where('orders_parcel_tracking_code_id = :trackingCodeId')
            ->setParameter('trackingCodeId', $id->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($trackingCodeData === false) {
            throw TrackingCodeNotFoundException::forId($id->value());
        }
        
        return $trackingCodeData;
    }
    
    
    /**
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function getFilteredTrackingCodesData(Filters $filters, Sorting $sorting, Pagination $pagination): array
    {
        $query = $this->db->createQueryBuilder()
            ->select('orders_parcel_tracking_codes.*, languages.code as `language_code`')
            ->from('orders_parcel_tracking_codes')
            ->join('orders_parcel_tracking_codes',
                   'languages',
                   'languages',
                   'orders_parcel_tracking_codes.language_id = languages.languages_id');
        $filters->applyToQuery($query);
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getTrackingCodesTotalCount(Filters $filters): int
    {
        $query = $this->db->createQueryBuilder()
            ->select('orders_parcel_tracking_codes.orders_parcel_tracking_code_id')
            ->from('orders_parcel_tracking_codes')
            ->join('orders_parcel_tracking_codes',
                   'languages',
                   'languages',
                   'orders_parcel_tracking_codes.language_id = languages.languages_id')
            ->groupBy('orders_parcel_tracking_codes.orders_parcel_tracking_code_id');
        $filters->applyToQuery($query);
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function getAllTrackingCodesData(): array
    {
        return $this->db->createQueryBuilder()
            ->select('orders_parcel_tracking_codes.*, languages.code as `language_code`')
            ->from('orders_parcel_tracking_codes')
            ->join('orders_parcel_tracking_codes',
                   'languages',
                   'languages',
                   'orders_parcel_tracking_codes.language_id = languages.languages_id')
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param OrderId $id
     *
     * @return array
     * @throws Exception
     */
    public function getTrackingCodesDataByOrderId(OrderId $id): array
    {
        return $this->db->createQueryBuilder()
            ->select('orders_parcel_tracking_codes.*, languages.code as `language_code`')
            ->from('orders_parcel_tracking_codes')
            ->join('orders_parcel_tracking_codes',
                   'languages',
                   'languages',
                   'orders_parcel_tracking_codes.language_id = languages.languages_id')
            ->where('order_id = :orderId')
            ->setParameter('orderId', $id->value())
            ->orderBy('orders_parcel_tracking_codes.orders_parcel_tracking_code_id')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}