<?php
/* --------------------------------------------------------------
   WithdrawalWriteService.inc.php 2018-01-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalWriteService
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Services
 */
class WithdrawalWriteService implements WithdrawalWriteServiceInterface
{
    /**
     * @var WithdrawalFactory
     */
    protected $withdrawalFactory;
    
    
    /**
     * WithdrawalWriteService constructor.
     *
     * @param \WithdrawalFactory $withdrawalFactory
     */
    public function __construct(WithdrawalFactory $withdrawalFactory)
    {
        $this->withdrawalFactory = $withdrawalFactory;
    }
    
    
    /**
     * Returns new instances of withdrawal entities
     *
     * @return Withdrawal New withdrawal entity.
     */
    public function createWithdrawal()
    {
        return $this->withdrawalFactory->createEntity();
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
        return $this->withdrawalFactory->createOrder($orderId,
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
     * Saves withdrawal entity in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal unit to be saved.
     *
     * @return $this|\WithdrawalWriteServiceInterface Same instance for chained method calls.
     */
    public function store(WithdrawalInterface $withdrawal)
    {
        $withdrawal->store();
        
        return $this;
    }
    
    
    /**
     * Deletes withdrawal entity from database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal entity to be deleted.
     *
     * @return $this|\WithdrawalWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(WithdrawalInterface $withdrawal)
    {
        $withdrawal->delete();
        
        return $this;
    }
}
