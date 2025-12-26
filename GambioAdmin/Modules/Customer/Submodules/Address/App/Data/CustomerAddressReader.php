<?php
/*--------------------------------------------------------------
   CustomerAddressReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CustomerAddressDoesNotExistException;

/**
 * Class CustomerAddressReader
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App\Data
 */
class CustomerAddressReader
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
     * Returns the default address of a customer
     *
     * @param CustomerId $customerId
     *
     * @return array
     * @throws CustomerAddressDoesNotExistException
     * @throws Exception
     */
    public function getDefaultAddress(CustomerId $customerId): array
    {
        $result = $this->createQueryBuilderForDefaultAddresses()
            ->where('c.customers_id = :customers_id')
            ->setParameter('customers_id', $customerId->value())
            ->executeQuery()
            ->fetchAllAssociative();
    
        if (count($result) === 0) {
            
            throw CustomerAddressDoesNotExistException::forCustomerId($customerId);
        }
    
        return array_shift($result);
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function createQueryBuilderForDefaultAddresses(): QueryBuilder
    {
        $columns = [
            'c.customers_default_address_id',
            'ab.address_book_id',
            'ab.customers_id',
            'ab.entry_gender',
            'ab.entry_company',
            'ab.entry_firstname',
            'ab.entry_lastname',
            'ab.entry_street_address',
            'ab.entry_house_number',
            'ab.entry_additional_info',
            'ab.entry_suburb',
            'ab.entry_postcode',
            'ab.entry_city',
            'count.countries_name',
            'count.countries_iso_code_2',
            'ab.entry_state',
            'ab.entry_country_id',
            'ab.entry_zone_id',
            'ab.address_date_added',
            'ab.address_last_modified',
        ];
    
        return $this->connection->createQueryBuilder()
            ->select(implode(',', $columns))
            ->from('customers', 'c')
            ->innerJoin('c', 'address_book', 'ab', 'c.customers_default_address_id=ab.address_book_id')
            ->innerJoin('ab', 'countries', 'count', 'ab.entry_country_id=count.countries_id')
            ->groupBy(implode(',', $columns));
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return array
     * @throws CustomerAddressDoesNotExistException
     * @throws Exception
     */
    public function getCustomerAddresses(CustomerId $customerId): array
    {
        $columns = [
            'ab.address_book_id',
            'ab.customers_id',
            'ab.entry_gender',
            'ab.entry_company',
            'ab.entry_firstname',
            'ab.entry_lastname',
            'ab.entry_street_address',
            'ab.entry_house_number',
            'ab.entry_additional_info',
            'ab.entry_suburb',
            'ab.entry_postcode',
            'ab.entry_city',
            'count.countries_name',
            'count.countries_iso_code_2',
            'ab.entry_state',
            'ab.entry_country_id',
            'ab.entry_zone_id',
            'ab.address_date_added',
            'ab.address_last_modified',
        ];
    
        $result = $this->connection->createQueryBuilder()
            ->select(implode(',', $columns))
            ->from('address_book', 'ab')
            ->innerJoin('ab', 'countries', 'count', 'ab.entry_country_id=count.countries_id')
            ->groupBy(implode(',', $columns))
            ->where('ab.customers_id = :customers_id')
            ->setParameter('customers_id', $customerId->value())
            ->executeQuery()
            ->fetchAllAssociative();
    
        if (count($result) === 0) {
        
            throw CustomerAddressDoesNotExistException::forCustomerId($customerId);
        }
        
        return $result;
    }
    
    
    /**
     * @param string $countryIsoCode
     * @param string $stateName
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function getStateId(string $countryIsoCode, string $stateName): int
    {
        $result = $this->connection->createQueryBuilder()
            ->select('zone_id')
            ->from('zones', 'z')
            ->join('z', 'countries', 'c', 'c.countries_id = z.zone_country_id')
            ->where('c.countries_iso_code_2 = :country_iso_code')
            ->andWhere('z.zone_name = :state_name')
            ->setParameter('country_iso_code', $countryIsoCode)
            ->setParameter('state_name', $stateName)
            ->executeQuery()
            ->fetchAssociative();
        
        return isset($result['zone_id']) ? (int)$result['zone_id'] : 0;
    }
}