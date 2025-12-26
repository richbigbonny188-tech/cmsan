<?php
/* --------------------------------------------------------------
   WithdrawalMapper.php 2022-12-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App\Data;

use Gambio\Admin\Modules\Withdrawal\Model\Collections\WithdrawalIds;
use Gambio\Admin\Modules\Withdrawal\Model\Collections\Withdrawals;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalFactory;

/**
 * Class WithdrawalMapper
 *
 * @package Gambio\Admin\Modules\Withdrawal\App\Data
 */
class WithdrawalMapper
{
    /**
     * @var WithdrawalFactory
     */
    private $factory;
    
    
    /**
     * WithdrawalMapper constructor.
     *
     * @param WithdrawalFactory $factory
     */
    public function __construct(WithdrawalFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $data
     *
     * @return Withdrawal
     */
    public function mapWithdrawal(array $data): Withdrawal
    {
        $orderId = ($data['order_id'] !== null) ? (int)$data['order_id'] : $data['order_id'];
        $order   = $this->factory->createOrderDetails($orderId,
                                                      $data['order_date'],
                                                      $data['delivery_date']);
        
        $address = $this->factory->createCustomerAddress($data['customer_street_address'],
                                                         $data['customer_postcode'],
                                                         $data['customer_city'],
                                                         $data['customer_country']);
        
        $customerId = $data['customer_id'] > 0 ? (int)$data['customer_id'] : null;
        $customer   = $this->factory->createCustomerDetails($data['customer_email'],
                                                            $address,
                                                            $customerId,
                                                            $data['customer_gender'],
                                                            $data['customer_firstname'],
                                                            $data['customer_lastname']);
        
        return $this->factory->createWithdrawal((int)$data['withdrawal_id'],
                                                $order,
                                                $customer,
                                                $data['withdrawal_date'],
                                                $data['withdrawal_content'],
                                                (bool)$data['created_by_admin'],
                                                $data['date_created']);
    }
    
    
    /**
     * @param array $data
     *
     * @return Withdrawals
     */
    public function mapWithdrawals(array $data): Withdrawals
    {
        $withdrawals = array_map([$this, 'mapWithdrawal'], $data);
        
        return $this->factory->createWithdrawals(...$withdrawals);
    }
    
    
    /**
     * @param int $id
     *
     * @return WithdrawalId
     */
    public function mapWithdrawalId(int $id): WithdrawalId
    {
        return $this->factory->createWithdrawalId($id);
    }
    
    
    /**
     * @param int ...$ids
     *
     * @return WithdrawalIds
     */
    public function mapWithdrawalIds(int ...$ids): WithdrawalIds
    {
        $ids = array_map([$this, 'mapWithdrawalId'], $ids);
        
        return $this->factory->createWithdrawalIds(...$ids);
    }
}