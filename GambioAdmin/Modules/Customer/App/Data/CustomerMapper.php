<?php
/*--------------------------------------------------------------
   CustomerMapper.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Data;

use DateTimeImmutable;
use Exception;
use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\Exceptions\EmailAddressIsInvalidException;

/**
 * Class CustomerMapper
 *
 * @package Gambio\Admin\Modules\Customer\App\Data
 */
class CustomerMapper extends CustomerFactory
{
    /**
     * @param array ...$customers
     *
     * @return Customers
     */
    public function mapCustomers(array ...$customers): Customers
    {
        return $this->createCustomers(...array_map([$this, 'mapCustomer'], $customers));
    }
    
    
    /**
     * @param array $customer
     *
     * @return Customer
     * @throws EmailAddressIsInvalidException
     * @throws Exception
     */
    public function mapCustomer(array $customer): Customer
    {
        $customerId     = $this->createCustomerId((int)$customer['customers_id']);
        $customerGroup  = $this->createCustomerGroup((int)$customer['customers_status']);
        $personalInfo   = $this->createPersonalInformation($customer['customers_gender'],
                                                           $customer['customers_firstname'],
                                                           $customer['customers_lastname'],
                                                           $customer['customers_cid'] ?? '',
                                                           new DateTimeImmutable($customer['customers_dob']));
        $businessInfo   = $this->createBusinessInformation($customer['customers_company'],
                                                           $customer['customers_vat_id'] ?? '',
                                                           $customer['customers_is_tradesperson'] === '1',
                                                           $customer['customers_vat_id_status'] === '1');
        $contactInfo    = $this->createContactInformation($customer['customers_email_address'],
                                                          $customer['customers_telephone'] ?? '',
                                                          $customer['customers_fax'] ?? '');
        $credit         = $this->createCredit((float)$customer['amount']);
        $isGuestAccount = $customer['account_type'] === '1';
        $isFavorite     = $customer['customers_is_favorite'] === '1';
        
        return Customer::create($customerId,
                                $customerGroup,
                                $personalInfo,
                                $businessInfo,
                                $contactInfo,
                                $credit,
                                $isGuestAccount,
                                $isFavorite);
    }
}