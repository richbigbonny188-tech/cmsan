<?php
/*--------------------------------------------------------------
   CustomerWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Exception;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerCredit;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Services\Exceptions\ChangingOfMainAdminCustomerGroupNotPermittedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CreationOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfMainAdminNotPermittedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\GuestCustomersBalanceCantBeChangedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\GuestCustomersCantBeCreatedWithABalanceException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\StorageOfCustomerFailedException;
use Gambio\Core\VatValidation\Services\VatValidationService;

/**
 * Class CustomerWriter
 *
 * @package Gambio\Admin\Modules\Customer\App\Data
 */
class CustomerWriter
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    /**
     * @var VatValidationService
     */
    private VatValidationService $vatValidationService;
    
    
    /**
     * @param Connection           $connection
     * @param VatValidationService $vatValidationService
     */
    public function __construct(Connection $connection, VatValidationService $vatValidationService)
    {
        $this->connection           = $connection;
        $this->vatValidationService = $vatValidationService;
    }
    
    
    /**
     * Creates a new customer and returns its ID.
     *
     * @param CustomerGroup       $customerGroup
     * @param PersonalInformation $personalInformation
     * @param BusinessInformation $businessInformation
     * @param ContactInformation  $contactInformation
     * @param CustomerCredit      $credit
     * @param bool                $isFavorite
     * @param bool                $isGuestAccount
     *
     * @return int
     *
     * @throws CreationOfCustomerFailedException
     * @throws CustomerEmailAddressMustBeUniqueException
     */
    public function createCustomer(
        CustomerGroup       $customerGroup,
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        CustomerCredit      $credit,
        bool                $isFavorite,
        bool                $isGuestAccount
    ): int {
        try {
            $emailAddressInput = $contactInformation->email();
            
            if ($this->emailAddressIsAlreadyTaken($emailAddressInput)) {
                if ($isGuestAccount === false) {
                    throw CustomerEmailAddressMustBeUniqueException::fromEmailAddress($emailAddressInput);
                }
                
                $this->deleteGuestByEmail($emailAddressInput);
            }
            $accountCreationDateTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            
            $this->connection->createQueryBuilder()
                ->insert('customers')
                ->setValue('customers_gender', ':customers_gender')
                ->setValue('customers_firstname', ':customers_firstname')
                ->setValue('customers_lastname', ':customers_lastname')
                ->setValue('customers_cid', ':customers_cid')
                ->setValue('customers_dob', ':customers_dob')
                ->setValue('customers_vat_id', ':customers_vat_id')
                ->setValue('customers_vat_id_status', ':customers_vat_id_status')
                ->setValue('customers_status', ':customers_status')
                ->setValue('customers_email_address', ':customers_email_address')
                ->setValue('customers_telephone', ':customers_telephone')
                ->setValue('customers_fax', ':customers_fax')
                ->setValue('account_type', ':account_type')
                ->setValue('customers_is_favorite', ':customers_is_favorite')
                ->setValue('customers_is_tradesperson', ':customers_is_tradesperson')
                ->setValue('customers_company', ':customers_company')
                ->setValue('customers_date_added', ':customers_date_added')
                ->setParameter('customers_gender', $personalInformation->gender())
                ->setParameter('customers_firstname', $personalInformation->firstName())
                ->setParameter('customers_lastname', $personalInformation->lastName())
                ->setParameter('customers_dob', $personalInformation->dateOfBirth())
                ->setParameter('customers_cid', $personalInformation->customerNumber())
                ->setParameter('customers_company', $businessInformation->companyName())
                ->setParameter('customers_vat_id', $businessInformation->vatId())
                ->setParameter('customers_vat_id_status',
                               $this->vatValidationService->validateVatId($businessInformation->vatId()))
                ->setParameter('customers_status', $customerGroup->id())
                ->setParameter('customers_email_address', $contactInformation->email())
                ->setParameter('customers_telephone', $contactInformation->phoneNumber())
                ->setParameter('customers_fax', $contactInformation->faxNumber())
                ->setParameter('account_type', $isGuestAccount ? 1 : 0)
                ->setParameter('customers_is_favorite', $isFavorite ? 1 : 0)
                ->setParameter('customers_is_tradesperson', $businessInformation->isTradesperson() ? 1 : 0)
                ->setParameter('customers_date_added', $accountCreationDateTime)
                ->executeQuery();
            
            $customerId = (int)$this->connection->lastInsertId();
            
            if ($credit->value() !== 0.0) {
                if ($isGuestAccount === true) {
                    throw GuestCustomersCantBeCreatedWithABalanceException::create();
                }
                
                $this->connection->createQueryBuilder()
                    ->insert('coupon_gv_customer')
                    ->setValue('customer_id', ':customer_id')
                    ->setValue('amount', ':amount')
                    ->setParameter('customer_id', $customerId)
                    ->setParameter('amount', $credit->value())
                    ->executeQuery();
            }
            
            $this->connection->createQueryBuilder()
                ->insert('customers_info')
                ->setValue('customers_info_id', ':customer_id')
                ->setValue('customers_info_number_of_logons', ':number_of_logons')
                ->setValue('customers_info_date_account_created', ':date_account_created')
                ->setParameter('customer_id', $customerId)
                ->setParameter('number_of_logons', 0)
                ->setParameter('date_account_created', $accountCreationDateTime)
                ->executeQuery();
            
            return $customerId;
        } catch (GuestCustomersCantBeCreatedWithABalanceException $exception) {
            throw $exception;
        } catch (CustomerEmailAddressMustBeUniqueException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw CreationOfCustomerFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * Creates multiple customers and returns their IDs.
     *
     * @param array ...$creationArguments
     *
     * @return int[]
     *
     * @throws CreationOfCustomerFailedException
     * @throws CustomerEmailAddressMustBeUniqueException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultipleCustomers(array ...$creationArguments): array
    {
        try {
            $this->connection->beginTransaction();
            $ids = [];
            
            foreach ($creationArguments as $creationArgument) {
                $ids[] = $this->createCustomer(...$creationArgument);
            }
            $this->connection->commit();
        } catch (CreationOfCustomerFailedException|CustomerEmailAddressMustBeUniqueException $exception) {
            $this->connection->rollBack();
            throw $exception;
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw CreationOfCustomerFailedException::becauseOfException($exception);
        }
        
        return $ids;
    }
    
    
    /**
     * @param string   $email
     * @param int|null $customerId
     *
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    private function emailAddressIsAlreadyTaken(string $email, ?int $customerId = null): bool
    {
        $query = $this->connection->createQueryBuilder()
            ->select('customers_email_address')
            ->from('customers', 'c')
            ->groupBy('customers_email_address')
            ->where('customers_email_address = :customers_email_address')
            ->setParameter('customers_email_address', $email);
        
        if ($customerId !== null) {
            $query->andWhere('customers_id != :customers_id')->setParameter('customers_id', $customerId);
        }
        
        return $query->executeQuery()->rowCount() === 1;
    }
    
    
    /**
     * Stores multiple customers.
     *
     * @param Customer ...$customers
     *
     * @return void
     *
     * @throws StorageOfCustomerFailedException
     * @throws CustomerEmailAddressMustBeUniqueException
     * @throws ChangingOfMainAdminCustomerGroupNotPermittedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeCustomers(Customer ...$customers): void
    {
        try {
            $this->connection->beginTransaction();
            array_map([$this, 'storeCustomer'], $customers);
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            $domainException = $exception instanceof CustomerEmailAddressMustBeUniqueException
                               || $exception instanceof ChangingOfMainAdminCustomerGroupNotPermittedException
                               || $exception instanceof GuestCustomersBalanceCantBeChangedException;
            throw $domainException ? $exception : StorageOfCustomerFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param Customer $customer
     *
     * @return void
     * @throws CustomerEmailAddressMustBeUniqueException
     * @throws ChangingOfMainAdminCustomerGroupNotPermittedException
     * @throws GuestCustomersBalanceCantBeChangedException
     * @throws \Doctrine\DBAL\Exception
     */
    private function storeCustomer(Customer $customer): void
    {
        if ($customer->isGuestAccount() === false
            && $this->emailAddressIsAlreadyTaken($customer->email(),
                                                 $customer->id())) {
            throw CustomerEmailAddressMustBeUniqueException::fromEmailAddress($customer->email());
        }
        
        if ($customer->customerGroup() !== 0 && $customer->id() === 1) {
            throw ChangingOfMainAdminCustomerGroupNotPermittedException::create();
        }
        
        $query = $this->connection->createQueryBuilder()
            ->update('customers')
            ->set('customers_gender', ':customers_gender')
            ->set('customers_firstname', ':customers_firstname')
            ->set('customers_lastname', ':customers_lastname')
            ->set('customers_cid', ':customers_cid')
            ->set('customers_dob', ':customers_dob')
            ->set('customers_vat_id', ':customers_vat_id')
            ->set('customers_vat_id_status', ':customers_vat_id_status')
            ->set('customers_email_address', ':customers_email_address')
            ->set('customers_telephone', ':customers_telephone')
            ->set('customers_fax', ':customers_fax')
            ->set('customers_is_favorite', ':customers_is_favorite')
            ->set('customers_is_tradesperson', ':customers_is_tradesperson')
            ->set('customers_company', ':customers_company')
            ->set('customers_status', ':customers_status')
            ->setParameter('customers_gender', $customer->gender())
            ->setParameter('customers_firstname', $customer->firstName())
            ->setParameter('customers_lastname', $customer->lastName())
            ->setParameter('customers_cid', $customer->customerNumber())
            ->setParameter('customers_dob', $customer->dateOfBirth())
            ->setParameter('customers_company', $customer->companyName())
            ->setParameter('customers_vat_id', $customer->vatId())
            ->setParameter('customers_vat_id_status',
                           $this->vatValidationService->validateVatId($customer->vatId()))
            ->setParameter('customers_email_address', $customer->email())
            ->setParameter('customers_telephone', $customer->phoneNumber())
            ->setParameter('customers_fax', $customer->faxNumber())
            ->setParameter('customers_is_favorite', $customer->isFavorite() ? 1 : 0)
            ->setParameter('customers_is_tradesperson', $customer->isTradesperson() ? 1 : 0)
            ->setParameter('customers_status', $customer->customerGroup());
        
        $query->where('customers_id = :customers_id')->setParameter('customers_id', $customer->id())->executeQuery();
        
        if ($customer->credit() !== 0.0 && $customer->isGuestAccount()) {
            throw GuestCustomersBalanceCantBeChangedException::forCustomer($customer);
        }
        
        $query = 'REPLACE INTO `coupon_gv_customer` (`customer_id`, `amount`) VALUES (:customer_id, :amount);';
        $stmt  = $this->connection->prepare($query);
        
        $stmt->bindValue(':customer_id', $customer->id());
        $stmt->bindValue(':amount', $customer->credit());
        $stmt->executeQuery();
    }
    
    
    /**
     * Deletes customers based on the given customer IDs.
     *
     * @param CustomerId ...$ids
     *
     * @return void
     *
     * @throws DeletionOfCustomerFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteCustomers(CustomerId ...$ids): void
    {
        try {
            $this->connection->beginTransaction();
            array_map([$this, 'deleteCustomer'], $ids);
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            if ($exception instanceof DeletionOfMainAdminNotPermittedException) {
                throw $exception;
            }
            
            throw DeletionOfCustomerFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * Deletes all guests customers
     *
     * @return int[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteOutdatedGuestAccounts(): array
    {
        $deletedAccounts = [];
        $guestQuery      = '
            SELECT c.`customers_id` FROM `customers` AS c
            LEFT JOIN whos_online AS wo ON c.`customers_id`=wo.`customer_id`
            WHERE c.account_type = 1 AND wo.`full_name` IS NULL';
        
        $result = $this->connection->prepare($guestQuery)->executeQuery();
        
        if ($result->rowCount() !== 0) {
            while ($row = $result->fetchAssociative()) {
                $deletedAccounts[] = (int)$row['customers_id'];
            }
            
            $guestAccountIds = array_map(fn(int $id): CustomerId => CustomerId::create($id), $deletedAccounts);
            $this->deleteCustomers(...$guestAccountIds);
        }
        
        return $deletedAccounts;
    }
    
    
    /**
     * @param CustomerId $id
     *
     * @return void
     * @throws DeletionOfMainAdminNotPermittedException
     */
    private function deleteCustomer(CustomerId $id): void
    {
        if ($id->value() === 1) {
            throw DeletionOfMainAdminNotPermittedException::create();
        }
        
        $this->connection->delete('admin_access_users', ['customer_id' => $id->value()]);
        $this->connection->delete('customers', ['customers_id' => $id->value()]);
        $this->connection->delete('customers_basket', ['customers_id' => $id->value()]);
        $this->connection->delete('customers_basket_attributes', ['customers_id' => $id->value()]);
        $this->connection->delete('customers_info', ['customers_info_id' => $id->value()]);
        $this->connection->delete('customers_ip', ['customers_id' => $id->value()]);
        $this->connection->delete('customers_status_history', ['customers_id' => $id->value()]);
        $this->connection->delete('customers_wishlist', ['customers_id' => $id->value()]);
        $this->connection->delete('customers_wishlist_attributes', ['customers_id' => $id->value()]);
        $this->connection->delete('coupon_gv_customer', ['customer_id' => $id->value()]);
        $this->connection->delete('gm_gprint_cart_elements', ['customers_id' => $id->value()]);
        $this->connection->delete('gm_gprint_wishlist_elements', ['customers_id' => $id->value()]);
        $this->connection->delete('whos_online', ['customer_id' => $id->value()]);
        
        $this->connection->update('coupon_gv_queue', ['customer_id' => 0], ['customer_id' => $id->value()]);
        $this->connection->update('coupon_redeem_track', ['customer_id' => 0], ['customer_id' => $id->value()]);
        $this->connection->update('gm_gprint_uploads', ['customers_id' => 0], ['customers_id' => $id->value()]);
        $this->connection->update('newsletter_recipients', ['customers_id' => 0], ['customers_id' => $id->value()]);
        $this->connection->update('orders', ['customers_id' => 0], ['customers_id' => $id->value()]);
        $this->connection->update('withdrawals', ['customer_id' => null], ['customer_id' => $id->value()]);
        
        $this->connection->createQueryBuilder()
            ->delete('customers')
            ->where('customers_id = :customers_id')
            ->setParameter('customers_id', $id->value())
            ->executeQuery();
    }
    
    
    /**
     * Deletes a guest account by its email address.
     *
     * @param string $email Guest customer's E-Mail address.
     */
    private function deleteGuestByEmail(string $email): void
    {
        $this->connection->createQueryBuilder()
            ->delete('customers')
            ->where('account_type = :account_type') // target guest accounts only
            ->setParameter('account_type', 1)
            ->setParameter('customers_email_address', $email)
            ->executeQuery();
    }
}