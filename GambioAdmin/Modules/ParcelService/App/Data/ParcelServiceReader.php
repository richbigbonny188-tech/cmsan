<?php
/* --------------------------------------------------------------
   ParcelServiceReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\ParcelServiceNotFoundException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class ParcelServiceReader
 *
 * @package Gambio\Admin\Modules\ParcelService\App\Data
 */
class ParcelServiceReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * ParcelServiceReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param ParcelServiceId $id
     *
     * @return array
     *
     * @throws ParcelServiceNotFoundException
     * @throws Exception
     */
    public function getParcelServiceDataById(ParcelServiceId $id): array
    {
        $parcelServiceData = $this->db->createQueryBuilder()
            ->select('*')
            ->from('parcel_services')
            ->where('parcel_service_id = :parcelServiceId')
            ->setParameter('parcelServiceId', $id->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($parcelServiceData === false) {
            throw ParcelServiceNotFoundException::forId($id->value());
        }
        
        $parcelServiceData['descriptions'] = $this->getParcelServiceDescriptionsDataById($id->value());
        
        return $parcelServiceData;
    }
    
    
    /**
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    private function getParcelServiceDescriptionsDataById(int $id): array
    {
        return $this->db->createQueryBuilder()
            ->select('parcel_services_description.*, languages.code AS language_code')
            ->from('parcel_services_description')
            ->join('parcel_services_description',
                   'languages',
                   'languages',
                   'parcel_services_description.language_id = languages.languages_id')
            ->where('parcel_service_id = :parcelServiceId')
            ->setParameter('parcelServiceId', $id)
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function getFilteredParcelServicesData(Filters $filters, Sorting $sorting, Pagination $pagination): array
    {
        $query = $this->db->createQueryBuilder()
            ->distinct()
            ->select('parcel_services.*')
            ->from('parcel_services')
            ->join('parcel_services',
                   'parcel_services_description',
                   'parcel_services_description',
                   'parcel_services.parcel_service_id = parcel_services_description.parcel_service_id')
            ->join('parcel_services_description',
                   'languages',
                   'languages',
                   'parcel_services_description.language_id = languages.languages_id');
        
        $filters->applyToQuery($query);
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return array_map(function (array $parcelServiceData) {
            $parcelServiceData['descriptions'] = $this->getParcelServiceDescriptionsDataById((int)$parcelServiceData['parcel_service_id']);
            
            return $parcelServiceData;
        }, $query->executeQuery()->fetchAllAssociative());
    }
    
    
    /**
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getParcelServicesTotalCount(Filters $filters): int
    {
        $query = $this->db->createQueryBuilder()
            ->distinct()
            ->select('parcel_services.parcel_service_id')
            ->from('parcel_services')
            ->join('parcel_services',
                   'parcel_services_description',
                   'parcel_services_description',
                   'parcel_services.parcel_service_id = parcel_services_description.parcel_service_id')
            ->join('parcel_services_description',
                   'languages',
                   'languages',
                   'parcel_services_description.language_id = languages.languages_id');
        $filters->applyToQuery($query);
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function getAllParcelServicesData(): array
    {
        $query = $this->db->createQueryBuilder()->select('parcel_services.*')->from('parcel_services');
        
        return array_map(function (array $parcelServiceData) {
            $parcelServiceData['descriptions'] = $this->getParcelServiceDescriptionsDataById((int)$parcelServiceData['parcel_service_id']);
            
            return $parcelServiceData;
        }, $query->executeQuery()->fetchAllAssociative());
    }
}