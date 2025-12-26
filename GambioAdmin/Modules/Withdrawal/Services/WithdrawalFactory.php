<?php
/* --------------------------------------------------------------
   WithdrawalFactory.php 2022-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Services;

use DateTime;
use Exception;
use Gambio\Admin\Modules\Withdrawal\Model\Collections\WithdrawalIds;
use Gambio\Admin\Modules\Withdrawal\Model\Collections\Withdrawals;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerAddress;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerGender;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use InvalidArgumentException;

/**
 * Class WithdrawalFactory
 *
 * @package Gambio\Admin\Modules\Withdrawal\Services
 */
class WithdrawalFactory
{
    /**
     * Creates and returns an order ID.
     *
     * @param int $id
     *
     * @return OrderId
     */
    public function createOrderId(int $id): OrderId
    {
        return OrderId::create($id);
    }
    
    
    /**
     * Creates and returns an withdrawal ID.
     *
     * @param int $id
     *
     * @return WithdrawalId
     */
    public function createWithdrawalId(int $id): WithdrawalId
    {
        return WithdrawalId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of withdrawal IDs.
     *
     * @param WithdrawalId ...$ids
     *
     * @return WithdrawalIds
     */
    public function createWithdrawalIds(WithdrawalId ...$ids): WithdrawalIds
    {
        return WithdrawalIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns a withdrawal.
     *
     * @param int             $id
     * @param OrderDetails    $order
     * @param CustomerDetails $customer
     * @param string|null     $date
     * @param string          $content
     * @param bool            $createdByAdmin
     * @param string|null     $createdOn
     *
     * @return Withdrawal
     */
    public function createWithdrawal(
        int $id,
        OrderDetails $order,
        CustomerDetails $customer,
        ?string $date,
        string $content,
        bool $createdByAdmin,
        ?string $createdOn = null
    ): Withdrawal {
        try {
            $dateObj = new DateTime($date);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid datetime format for withdrawal date provided.');
        }
        try {
            $createdOnObj = ($createdOn !== null) ? new DateTime($createdOn) : null;
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid datetime format for withdrawal creation date provided.');
        }
        
        return Withdrawal::create(WithdrawalId::create($id),
                                  $order,
                                  $customer,
                                  $dateObj,
                                  $content,
                                  $createdByAdmin,
                                  $createdOnObj);
    }
    
    
    /**
     * Creates and returns a collection of withdrawals.
     *
     * @param Withdrawal ...$withdrawals
     *
     * @return Withdrawals
     */
    public function createWithdrawals(Withdrawal ...$withdrawals): Withdrawals
    {
        return Withdrawals::create(...$withdrawals);
    }
    
    
    /**
     * Creates and returns order details.
     *
     * @param int|null    $id
     * @param string|null $creationDate
     * @param string|null $deliveryDate
     *
     * @return OrderDetails
     */
    public function createOrderDetails(
        ?int $id = null,
        ?string $creationDate = null,
        ?string $deliveryDate = null
    ): OrderDetails {
        try {
            $creationDateObj = ($creationDate !== null) ? new DateTime($creationDate) : $creationDate;
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid datetime format for order creation date provided.');
        }
        try {
            $deliveryDateObj = ($deliveryDate !== null) ? new DateTime($deliveryDate) : $deliveryDate;
        } catch (Exception $e) {
            throw new InvalidArgumentException('Invalid datetime format for order delivery date provided.');
        }
        
        return OrderDetails::create(OrderId::create($id), $creationDateObj, $deliveryDateObj);
    }
    
    
    /**
     * Creates and returns customer details.
     *
     * @param string          $email
     * @param CustomerAddress $address
     * @param int|null        $id
     * @param string|null     $gender
     * @param string|null     $firstname
     * @param string|null     $lastname
     *
     * @return CustomerDetails
     */
    public function createCustomerDetails(
        string $email,
        CustomerAddress $address,
        ?int $id = null,
        ?string $gender = null,
        ?string $firstname = '',
        ?string $lastname = ''
    ): CustomerDetails {
        return CustomerDetails::create(CustomerId::create($id),
                                       CustomerGender::create($gender),
                                       $firstname,
                                       $lastname,
                                       $address,
                                       $email);
    }
    
    
    /**
     * Creates and returns a customer address.
     *
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $country
     *
     * @return CustomerAddress
     */
    public function createCustomerAddress(
        string $street = '',
        string $postcode = '',
        string $city = '',
        string $country = ''
    ): CustomerAddress {
        return CustomerAddress::create($street, $postcode, $city, $country);
    }
}