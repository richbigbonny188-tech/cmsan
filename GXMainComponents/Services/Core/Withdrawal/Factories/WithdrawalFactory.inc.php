<?php

/* --------------------------------------------------------------
   WithdrawalFactory.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalFactory
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Factories
 */
class WithdrawalFactory
{
    /**
     * @var \WithdrawalRepository
     */
    protected $repository;
    
    
    /**
     * WithdrawalFactory constructor.
     *
     * @param \WithdrawalRepository $repository
     */
    public function __construct(WithdrawalRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns new instances of withdrawal entities
     *
     * @return Withdrawal New withdrawal entity.
     */
    public function createEntity()
    {
        return new Withdrawal($this->repository);
    }
    
    
    /**
     * Returns a new instance of withdrawal order.
     *
     * @param \IntType    $orderId
     * @param \IntType    $customerId
     * @param \StringType $customerGender
     * @param \StringType $customerFirstName
     * @param \StringType $customerLastName
     * @param \StringType $customerStreetAddress
     * @param \IntType    $customerPostCode
     * @param \StringType $customerCity
     * @param \StringType $customerCountry
     * @param \StringType $customerEmail
     * @param \DateTime   $orderDate
     * @param \DateTime   $deliveryDate
     *
     * @return \WithdrawalOrder
     */
    public function createOrder(
        IntType $orderId,
        IntType $customerId,
        StringType $customerGender,
        StringType $customerFirstName,
        StringType $customerLastName,
        StringType $customerStreetAddress,
        IntType $customerPostCode,
        StringType $customerCity,
        StringType $customerCountry,
        StringType $customerEmail,
        DateTime $orderDate,
        DateTime $deliveryDate
    ) {
        return new WithdrawalOrder($orderId,
                                   $customerId,
                                   $customerGender,
                                   $customerFirstName,
                                   $customerLastName,
                                   $customerStreetAddress,
                                   $customerPostCode,
                                   $customerCity,
                                   $customerCountry,
                                   $customerEmail,
                                   $orderDate,
                                   $deliveryDate);
    }
    
    
    /**
     * Returns new instances of withdrawal collections.
     */
    public function createCollection()
    {
        return new WithdrawalCollection;
    }
}