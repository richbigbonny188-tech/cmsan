<?php
/* --------------------------------------------------------------
   CustomerAddonValueReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\Filter\CustomerAddonValueFilters;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\Filter\CustomerAddonValueSorting;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueKey;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueDoesNotExistException;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class CustomerAddonValueReader
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App\Data
 */
class CustomerAddonValueReader
{
    /**
     *
     */
    private const LEGACY_CONTAINER_TYPE = 'CustomerInterface';
    
    
    /**
     * @var Connection
     */
    private Connection $db;
    
    
    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param CustomerId            $customerId
     * @param CustomerAddonValueKey $key
     *
     * @return array
     *
     * @throws Exception
     */
    public function getCustomerAddonValue(CustomerId $customerId, CustomerAddonValueKey $key): array
    {
        $dbData = $this->db->createQueryBuilder()
            ->select('*')
            ->from('addon_values_storage')
            ->where('container_type = :containerType')
            ->andWhere('container_id = :customerId')
            ->andWhere('addon_key = :key')
            ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
            ->setParameter('customerId', $customerId->value())
            ->setParameter('key', $key->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($dbData === false) {
            throw CustomerAddonValueDoesNotExistException::forGivenCustomerAndKey($customerId->value(), $key->value());
        }
        
        return $dbData;
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCustomerAddonValues(CustomerId $customerId): array
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('addon_values_storage')
            ->where('container_type = :containerType')
            ->andWhere('container_id = :customerId')
            ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
            ->setParameter('customerId', $customerId->value())
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param CustomerId                $customerId
     * @param CustomerAddonValueFilters $filters
     * @param CustomerAddonValueSorting $sorting
     * @param Pagination|SqlPagination  $pagination
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function filterCustomerAddonValues(
        CustomerId                $customerId,
        CustomerAddonValueFilters $filters,
        CustomerAddonValueSorting $sorting,
        Pagination                $pagination
    ): array {
        $query = $this->db->createQueryBuilder()
            ->select('*')
            ->from('addon_values_storage')
            ->where('container_type = :containerType')
            ->andWhere('container_id = :customerId')
            ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
            ->setParameter('customerId', $customerId->value());
        
        $filters->applyToQuery($query);
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * @param CustomerId                $customerId
     * @param CustomerAddonValueFilters $filters
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCustomerAddonValuesTotalCount(CustomerId $customerId, CustomerAddonValueFilters $filters): int
    {
        $query = $this->db->createQueryBuilder()
            ->select('*')
            ->from('addon_values_storage')
            ->where('container_type = :containerType')
            ->andWhere('container_id = :customerId')
            ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
            ->setParameter('customerId', $customerId->value());
        
        $filters->applyToQuery($query);
        
        return $query->executeQuery()->rowCount();
    }
}