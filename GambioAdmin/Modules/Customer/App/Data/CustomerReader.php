<?php
/*--------------------------------------------------------------
   CustomerReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Customer\App\Data\Filter\CustomerSearch;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerFilters;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSearch as CustomerSearchByKeywordInterface;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSorting;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class CustomerReader
 *
 * @package Gambio\Admin\Modules\Customer\Services\App\Data
 */
class CustomerReader
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
     * @param bool $areGuestAccounts
     *
     * @return array
     * @throws Exception
     */
    public function getCustomers(bool $areGuestAccounts = false): array
    {
        $query = $this->createQuery();
        if ($areGuestAccounts) {
            $query = $query->where('c.account_type = :account_type')->setParameter('account_type', '1');
        }
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * Returns a specific customer based on the given ID.
     *
     * @param CustomerId $id
     *
     * @return array
     *
     * @throws CustomerDoesNotExistException
     * @throws Exception
     */
    public function getCustomerById(CustomerId $id): array
    {
        $result = $this->createQuery()
            ->where('c.customers_id = :customers_id')
            ->setParameter('customers_id', $id->value())
            ->executeQuery()
            ->fetchAllAssociative();
        
        if (count($result) === 0) {
            throw CustomerDoesNotExistException::forId($id);
        }
        
        return array_shift($result);
    }
    
    
    /**
     * @param CustomerFilters          $filters
     * @param CustomerSorting          $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function getFilteredCustomers(
        CustomerFilters $filters,
        CustomerSorting $sorting,
        Pagination      $pagination
    ): array {
        $filters->applyToQuery($query = $this->createQuery());
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * Returns a paginated collection of customers based on the given search term and sorting arguments.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param CustomerSearchByKeywordInterface|CustomerSearch $searchTerm
     * @param CustomerSorting                                 $sorting
     * @param Pagination|SqlPagination                        $pagination
     *
     * @return array
     * @throws Exception
     */
    public function searchCustomers(
        CustomerSearchByKeywordInterface $searchTerm,
        CustomerSorting                  $sorting,
        Pagination                       $pagination
    ): array {
        $searchTerm->applyToQuery($query = $this->createQuery());
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * @param CustomerSearchByKeywordInterface|CustomerSearch $search
     *
     * @return int
     * @throws Exception
     */
    public function getSearchedCustomerTotalCount(CustomerSearchByKeywordInterface $search): int
    {
        $search->applyToQuery($query = $this->createQuery());
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * @param CustomerFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getCustomerTotalCount(CustomerFilters $filters): int
    {
        $filters->applyToQuery($query = $this->createQuery());
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * @return QueryBuilder
     */
    public function createQuery(): QueryBuilder
    {
        $columns = $groupBy = [
            'c.customers_id',
            'c.customers_gender',
            'c.customers_firstname',
            'c.customers_lastname',
            'c.customers_cid',
            'c.customers_dob',
            'c.customers_company',
            'c.customers_vat_id',
            'c.customers_vat_id_status',
            'c.customers_status',
            'c.customers_email_address',
            'c.customers_telephone',
            'c.customers_fax',
            'c.customers_is_tradesperson',
            'gv.amount',
            'c.account_type',
            'c.customers_is_favorite',
        ];
        
        return $this->connection->createQueryBuilder()
            ->select(implode(',', $columns))
            ->from('customers', 'c')
            ->leftJoin('c', 'coupon_gv_customer', 'gv', 'c.customers_id=gv.customer_id');
    }
    
    
    /**
     * @param string $email
     *
     * @return bool
     * @throws Exception
     */
    public function emailAddressIsAlreadyTaken(string $email): bool
    {
        $query = $this->connection->createQueryBuilder()
            ->select('customers_email_address')
            ->from('customers', 'c')
            ->groupBy('customers_email_address')
            ->where('customers_email_address = :customers_email_address')
            ->setParameter('customers_email_address', $email);
        
        return $query->executeQuery()->rowCount() === 1;
    }
}