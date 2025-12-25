<?php
/* --------------------------------------------------------------
   WithdrawalWriter.inc.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalWriter
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
class WithdrawalWriter implements WithdrawalWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * WithdrawalWriter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Saves withdrawal entity data in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal to be saved.
     *
     * @return $this|\WithdrawalWriterInterface Same instance for chained method calls.
     */
    public function store(WithdrawalInterface $withdrawal)
    {
        $this->queryBuilder->set([
                                     'order_id'                => $withdrawal->getWithdrawalOrder()->getOrderId(),
                                     'customer_id'             => $withdrawal->getWithdrawalOrder()->getCustomerId(),
                                     'customer_gender'         => $withdrawal->getWithdrawalOrder()
                                         ->getCustomerGender(),
                                     'customer_firstname'      => $withdrawal->getWithdrawalOrder()
                                         ->getCustomerFirstName(),
                                     'customer_lastname'       => $withdrawal->getWithdrawalOrder()
                                         ->getCustomerLastName(),
                                     'customer_street_address' => $withdrawal->getWithdrawalOrder()
                                         ->getCustomerStreetAddress(),
                                     'customer_postcode'       => $withdrawal->getWithdrawalOrder()
                                         ->getCustomerPostCode(),
                                     'customer_city'           => $withdrawal->getWithdrawalOrder()->getCustomerCity(),
                                     'customer_country'        => $withdrawal->getWithdrawalOrder()
                                         ->getCustomerCountry(),
                                     'customer_email'          => $withdrawal->getWithdrawalOrder()->getCustomerEmail(),
                                     'order_date'              => $withdrawal->getWithdrawalOrder()
                                         ->getOrderDate()
                                         ->format('Y-m-d H:i:s'),
                                     'delivery_date'           => $withdrawal->getWithdrawalOrder()
                                         ->getDeliveryDate()
                                         ->format('Y-m-d H:i:s'),
                                     'withdrawal_date'         => $withdrawal->getWithdrawalDate()
                                         ->format('Y-m-d H:i:s'),
                                     'withdrawal_content'      => $withdrawal->getWithdrawalContent(),
                                     'created_by_admin'        => $withdrawal->getCreatedByAdmin(),
                                     'date_created'            => $withdrawal->getDateCreated()->format('Y-m-d H:i:s')
                                 ]);
        
        $this->queryBuilder->insert('withdrawals');
        
        $withdrawalId = $this->queryBuilder->insert_id();
        
        $withdrawal->setWithdrawalId(new IdType($withdrawalId));
        
        return $this;
    }
    
    
    /**
     * Updates withdrawal entity data in database.
     *
     * @param \WithdrawalInterface $withdrawal Withdrawal to be updated.
     *
     * @return $this|\WithdrawalWriterInterface Same instance for chained method calls.
     */
    public function update(WithdrawalInterface $withdrawal)
    {
        $this->queryBuilder->update('withdrawals',
                                    [
            
                                        'order_id'                => $withdrawal->getWithdrawalOrder()->getOrderId(),
                                        'customer_id'             => $withdrawal->getWithdrawalOrder()->getCustomerId(),
                                        'customer_gender'         => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerGender(),
                                        'customer_firstname'      => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerFirstName(),
                                        'customer_lastname'       => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerLastName(),
                                        'customer_street_address' => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerStreetAddress(),
                                        'customer_postcode'       => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerPostCode(),
                                        'customer_city'           => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerCity(),
                                        'customer_country'        => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerCountry(),
                                        'customer_email'          => $withdrawal->getWithdrawalOrder()
                                            ->getCustomerEmail(),
                                        'order_date'              => $withdrawal->getWithdrawalOrder()
                                            ->getOrderDate()
                                            ->format('Y-m-d H:i:s'),
                                        'delivery_date'           => $withdrawal->getWithdrawalOrder()
                                            ->getDeliveryDate()
                                            ->format('Y-m-d H:i:s'),
                                        'withdrawal_date'         => $withdrawal->getWithdrawalDate()
                                            ->format('Y-m-d H:i:s'),
                                        'withdrawal_content'      => $withdrawal->getWithdrawalContent(),
                                        'created_by_admin'        => $withdrawal->getCreatedByAdmin()
                                    ],
                                    ['withdrawal_id' => $withdrawal->getWithdrawalId()]);
        
        return $this;
    }
}