<?php
/* --------------------------------------------------------------
   WithdrawalsOrderIdUpdated.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\Events;

use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;

/**
 * Class WithdrawalsOrderIdUpdated
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\Events
 * @codeCoverageIgnore
 */
class WithdrawalsOrderIdUpdated
{
    /**
     * @var WithdrawalId
     */
    private $withdrawalId;
    
    /**
     * @var OrderId
     */
    private $orderId;
    
    
    /**
     * WithdrawalsOrderIdUpdated constructor.
     *
     * @param WithdrawalId $withdrawalId
     * @param OrderId      $orderId
     */
    private function __construct(WithdrawalId $withdrawalId, OrderId $orderId)
    {
        $this->withdrawalId = $withdrawalId;
        $this->orderId      = $orderId;
    }
    
    
    /**
     * @param WithdrawalId $withdrawalId
     * @param OrderId      $orderId
     *
     * @return WithdrawalsOrderIdUpdated
     */
    public static function create(WithdrawalId $withdrawalId, OrderId $orderId): WithdrawalsOrderIdUpdated
    {
        return new self($withdrawalId, $orderId);
    }
    
    
    /**
     * @return WithdrawalId
     */
    public function withdrawalId(): WithdrawalId
    {
        return $this->withdrawalId;
    }
    
    
    /**
     * @return OrderId
     */
    public function orderId(): OrderId
    {
        return $this->orderId;
    }
}