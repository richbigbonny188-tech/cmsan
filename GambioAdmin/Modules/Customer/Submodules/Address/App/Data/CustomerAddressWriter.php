<?php
/*--------------------------------------------------------------
   CustomerAddressWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CreationOfCustomerAddressFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\DeletionOfCustomerAddressFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\StorageOfCustomerAddressFailedException;
use RuntimeException;

/**
 * Class CustomerAddressWriter
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App\Data
 */
class CustomerAddressWriter
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
     * @param CustomerId          $customerId
     * @param PersonalInformation $personalInformation
     * @param LocationInformation $locationInformation
     * @param bool                $isDefault
     *
     * @return int
     * @throws CreationOfCustomerAddressFailedException
     */
    public function createCustomerAddress(
        CustomerId          $customerId,
        PersonalInformation $personalInformation,
        LocationInformation $locationInformation,
        bool                $isDefault = true
    ): int {
        try {
            $countryId = $this->getCountryIdByIsoCode2($locationInformation->countryIsoCode2());
            
            $this->connection->createQueryBuilder()
                ->insert('address_book')
                ->setValue('entry_country_id', ':entry_country_id')
                ->setValue('customers_id', ':customers_id')
                ->setValue('entry_gender', ':gender')
                ->setValue('entry_company', ':company')
                ->setValue('entry_firstname', ':firstname')
                ->setValue('entry_lastname', ':lastname')
                ->setValue('entry_street_address', ':street_address')
                ->setValue('entry_house_number', ':house_number')
                ->setValue('entry_additional_info', ':additional_info')
                ->setValue('entry_suburb', ':suburb')
                ->setValue('entry_postcode', ':postcode')
                ->setValue('entry_city', ':city')
                ->setValue('entry_state', ':state_name')
                ->setValue('entry_zone_id', ':state_id')
                ->setParameter('entry_country_id', $countryId)
                ->setParameter('customers_id', $customerId->value())
                ->setParameter('gender', $personalInformation->gender())
                ->setParameter('company', $personalInformation->companyName())
                ->setParameter('firstname', $personalInformation->firstName())
                ->setParameter('lastname', $personalInformation->lastName())
                ->setParameter('street_address', $locationInformation->streetName())
                ->setParameter('house_number', $locationInformation->houseNumber())
                ->setParameter('additional_info', $locationInformation->additionalInformation())
                ->setParameter('suburb', $locationInformation->suburb())
                ->setParameter('postcode', $locationInformation->postcode())
                ->setParameter('city', $locationInformation->city())
                ->setParameter('state_name', $locationInformation->stateName())
                ->setParameter('state_id', $locationInformation->stateId())
                ->executeQuery();
            
            $addressBookId = (int)$this->connection->lastInsertId();
            if ($isDefault === true) {
                $this->connection->createQueryBuilder()
                    ->update('customers')
                    ->set('customers_default_address_id', ':address_book_id')
                    ->setParameter('address_book_id', $addressBookId)
                    ->where('customers_id=:customers_id')
                    ->setParameter('customers_id', $customerId->value())
                    ->executeQuery();
            }
            
            return $addressBookId;
        } catch (Exception $exception) {
            throw CreationOfCustomerAddressFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param array ...$creationArguments
     *
     * @return array
     * @throws CreationOfCustomerAddressFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createCustomerAddresses(array ...$creationArguments): array
    {
        try {
            $this->connection->beginTransaction();
            $ids = [];
            
            foreach ($creationArguments as $creationArgument) {
                $ids[] = $this->createCustomerAddress(...$creationArgument);
            }
            $this->connection->commit();
            
            return $ids;
        } catch (CreationOfCustomerAddressFailedException $exception) {
            $this->connection->rollBack();
            throw $exception;
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw CreationOfCustomerAddressFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param bool            $isDefault
     * @param CustomerAddress ...$customerAddresses
     *
     * @return void
     * @throws StorageOfCustomerAddressFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeCustomerAddresses(bool $isDefault = true, CustomerAddress ...$customerAddresses): void
    {
        try {
            $this->connection->beginTransaction();
            
            foreach ($customerAddresses as $customerAddress) {
                $this->storeCustomerAddress($isDefault, $customerAddress);
            }
            
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw StorageOfCustomerAddressFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerAddress $customerAddress
     * @param bool            $isDefault
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function storeCustomerAddress(bool $isDefault, CustomerAddress $customerAddress): void
    {
        $this->connection->createQueryBuilder()
            ->update('address_book')
            ->set('entry_country_id', ':entry_country_id')
            ->set('entry_gender', ':gender')
            ->set('entry_company', ':company')
            ->set('entry_firstname', ':firstname')
            ->set('entry_lastname', ':lastname')
            ->set('entry_street_address', ':street_address')
            ->set('entry_house_number', ':house_number')
            ->set('entry_additional_info', ':additional_info')
            ->set('entry_suburb', ':suburb')
            ->set('entry_postcode', ':postcode')
            ->set('entry_city', ':city')
            ->set('entry_state', ':state_name')
            ->set('entry_zone_id', ':state_id')
            ->setParameter('entry_country_id', $this->getCountryIdByIsoCode2($customerAddress->countryIsoCode2()))
            ->setParameter('gender', $customerAddress->gender())
            ->setParameter('company', $customerAddress->companyName())
            ->setParameter('firstname', $customerAddress->firstName())
            ->setParameter('lastname', $customerAddress->lastName())
            ->setParameter('street_address', $customerAddress->streetName())
            ->setParameter('house_number', $customerAddress->houseNumber())
            ->setParameter('additional_info', $customerAddress->additionalInformation())
            ->setParameter('suburb', $customerAddress->suburb())
            ->setParameter('postcode', $customerAddress->postcode())
            ->setParameter('city', $customerAddress->city())
            ->setParameter('state_name', $customerAddress->stateName())
            ->setParameter('state_id', $customerAddress->stateId())
            ->where('address_book_id = :address_book_id')
            ->setParameter('address_book_id', $customerAddress->id())
            ->executeQuery();
        
        if ($isDefault === true) {
            $this->connection->createQueryBuilder()
                ->update('customers')
                ->set('customers_default_address_id', ':address_book_id')
                ->setParameter('address_book_id', $customerAddress->id())
                ->where('customers_id=:customers_id')
                ->setParameter('customers_id', $customerAddress->customerId())
                ->executeQuery();
        }
    }
    
    
    /**
     * @param string $countryIsoCode2
     *
     * @return int
     * @throws RuntimeException
     * @throws \Doctrine\DBAL\Exception
     */
    private function getCountryIdByIsoCode2(string $countryIsoCode2): int
    {
        $result = $this->connection->createQueryBuilder()
            ->select('countries_id')
            ->from('countries')
            ->where('countries_iso_code_2 = :countries_iso_code_2')
            ->setParameter('countries_iso_code_2', $countryIsoCode2)
            ->groupBy('countries_id')
            ->executeQuery()
            ->fetchAllAssociative();
        
        if (count($result) === 0) {
            throw new RuntimeException(sprintf('No country id found for country "%s"', $countryIsoCode2));
        }
        
        return (int)array_shift($result)['countries_id'];
    }
    
    
    /**
     * @param CustomerAddressId $addressId
     *
     * @return void
     * @throws DeletionOfCustomerAddressFailedException
     */
    public function deleteCustomerAddress(CustomerAddressId $addressId): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete('address_book')
                ->where('address_book_id = :address_book_id')
                ->setParameter('address_book_id', $addressId->value())
                ->executeQuery();
        } catch (Exception $exception) {
            throw DeletionOfCustomerAddressFailedException::becauseOfException($exception, $addressId);
        }
    }
}