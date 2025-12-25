<?php
/* --------------------------------------------------------------
   WithdrawalWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App\Data;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Exception;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\CreationOfWithdrawalFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\DeletionOfWithdrawalsFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\StorageOfWithdrawalsFailedException;

/**
 * Class WithdrawalWriter
 *
 * @package Gambio\Admin\Modules\Withdrawal\App\Data
 */
class WithdrawalWriter
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * WithdrawalWriter constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param OrderDetails    $order
     * @param CustomerDetails $customer
     * @param DateTime|null   $date
     * @param string          $content
     * @param bool            $createdByAdmin
     *
     * @return int
     *
     * @throws CreationOfWithdrawalFailedException
     */
    public function createWithdrawal(
        OrderDetails    $order,
        CustomerDetails $customer,
        ?DateTime       $date = null,
        string          $content = '',
        bool            $createdByAdmin = true
    ): int {
        try {
            $this->db->createQueryBuilder()
                ->insert('withdrawals')
                ->setValue('order_id', ':order_id')
                ->setValue('order_date', ':order_date')
                ->setValue('delivery_date', ':delivery_date')
                ->setValue('customer_id', ':customer_id')
                ->setValue('customer_gender', ':customer_gender')
                ->setValue('customer_firstname', ':customer_firstname')
                ->setValue('customer_lastname', ':customer_lastname')
                ->setValue('customer_street_address', ':customer_street_address')
                ->setValue('customer_postcode', ':customer_postcode')
                ->setValue('customer_city', ':customer_city')
                ->setValue('customer_country', ':customer_country')
                ->setValue('customer_email', ':customer_email')
                ->setValue('withdrawal_date', ':withdrawal_date')
                ->setValue('withdrawal_content', ':withdrawal_content')
                ->setValue('created_by_admin', ':created_by_admin')
                ->setValue('date_created', 'now()')
                ->setParameter('order_id', $order->id())
                ->setParameter('order_date', $order->creationDate())
                ->setParameter('delivery_date', $order->deliveryDate())
                ->setParameter('customer_id', $customer->id())
                ->setParameter('customer_gender', $customer->gender())
                ->setParameter('customer_firstname', $customer->firstName())
                ->setParameter('customer_lastname', $customer->lastName())
                ->setParameter('customer_street_address', $customer->street())
                ->setParameter('customer_postcode', $customer->postcode())
                ->setParameter('customer_city', $customer->city())
                ->setParameter('customer_country', $customer->country())
                ->setParameter('customer_email', $customer->email())
                ->setParameter('withdrawal_date', ($date !== null) ? $date->format('Y-m-d H:i:s') : null)
                ->setParameter('withdrawal_content', $content)
                ->setParameter('created_by_admin', $createdByAdmin ? 1 : 0)
                ->executeQuery();
            
            return (int)$this->db->lastInsertId();
        } catch (Exception $exception) {
            throw CreationOfWithdrawalFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return int[]
     *
     * @throws CreationOfWithdrawalFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultipleWithdrawals(array ...$creationArguments): array
    {
        try {
            $this->db->beginTransaction();
            $ids = [];
            foreach ($creationArguments as $index => $creationArgument) {
                $ids[] = $this->createWithdrawal($creationArgument[0],
                                                 $creationArgument[1],
                                                 $creationArgument[2],
                                                 $creationArgument[3],
                                                 $creationArgument[4]);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            if (!($exception instanceof CreationOfWithdrawalFailedException)) {
                $exception = CreationOfWithdrawalFailedException::becauseOfException($exception);
            }
            
            $this->db->rollBack();
            throw $exception;
        }
        
        return $ids;
    }
    
    
    /**
     * @param Withdrawal $withdrawal
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateWithdrawal(Withdrawal $withdrawal): void
    {
        $this->db->createQueryBuilder()
            ->update('withdrawals')
            ->set('order_id', ':order_id')
            ->set('order_date', ':order_date')
            ->set('delivery_date', ':delivery_date')
            ->set('customer_id', ':customer_id')
            ->set('customer_gender', ':customer_gender')
            ->set('customer_firstname', ':customer_firstname')
            ->set('customer_lastname', ':customer_lastname')
            ->set('customer_street_address', ':customer_street_address')
            ->set('customer_postcode', ':customer_postcode')
            ->set('customer_city', ':customer_city')
            ->set('customer_country', ':customer_country')
            ->set('customer_email', ':customer_email')
            ->set('withdrawal_date', ':withdrawal_date')
            ->set('withdrawal_content', ':withdrawal_content')
            ->set('created_by_admin', ':created_by_admin')
            ->where('withdrawal_id = :id')
            ->setParameter('order_id', $withdrawal->orderId())
            ->setParameter('order_date', $withdrawal->orderCreationDate())
            ->setParameter('delivery_date', $withdrawal->orderDeliveryDate())
            ->setParameter('customer_id', $withdrawal->customerId())
            ->setParameter('customer_gender', $withdrawal->customerGender())
            ->setParameter('customer_firstname', $withdrawal->customerFirstName())
            ->setParameter('customer_lastname', $withdrawal->customerLastName())
            ->setParameter('customer_street_address', $withdrawal->customerStreet())
            ->setParameter('customer_postcode', $withdrawal->customerPostcode())
            ->setParameter('customer_city', $withdrawal->customerCity())
            ->setParameter('customer_country', $withdrawal->customerCountry())
            ->setParameter('customer_email', $withdrawal->customerEmail())
            ->setParameter('withdrawal_date', $withdrawal->date())
            ->setParameter('withdrawal_content', $withdrawal->content())
            ->setParameter('created_by_admin', $withdrawal->wasCreatedByAdmin() ? 1 : 0)
            ->setParameter('id', $withdrawal->id())
            ->executeQuery();
    }
    
    
    /**
     * @param Withdrawal ...$withdrawals
     *
     * @throws ConnectionException
     * @throws StorageOfWithdrawalsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateWithdrawals(Withdrawal ...$withdrawals): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($withdrawals as $withdrawal) {
                $this->updateWithdrawal($withdrawal);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw StorageOfWithdrawalsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * Deletes a withdrawal from the database.
     *
     * @param WithdrawalId $id
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteWithdrawal(WithdrawalId $id): void
    {
        $this->db->createQueryBuilder()
            ->delete('withdrawals')
            ->where('withdrawal_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery();
    }
    
    
    /**
     * @param WithdrawalId ...$ids
     *
     * @throws ConnectionException
     * @throws DeletionOfWithdrawalsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteWithdrawals(WithdrawalId ...$ids): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($ids as $id) {
                $this->deleteWithdrawal($id);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw DeletionOfWithdrawalsFailedException::becauseOfException($exception);
        }
    }
}