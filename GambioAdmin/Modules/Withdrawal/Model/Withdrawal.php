<?php
/* --------------------------------------------------------------
   Withdrawal.php 2020-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model;

use DateTime;
use Gambio\Admin\Modules\Withdrawal\Model\Events\WithdrawalsOrderIdUpdated;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class Withdrawal
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model
 */
class Withdrawal extends AbstractEventRaisingEntity
{
    /**
     * @var WithdrawalId
     */
    private $id;
    
    /**
     * @var OrderDetails
     */
    private $order;
    
    /**
     * @var CustomerDetails
     */
    private $customer;
    
    /**
     * @var DateTime|null
     */
    private $date;
    
    /**
     * @var string
     */
    private $content;
    
    /**
     * @var bool
     */
    private $createdByAdmin;
    
    /**
     * @var DateTime|null
     */
    private $createdOn;
    
    
    /**
     * Withdrawal constructor.
     *
     * @param WithdrawalId    $id
     * @param OrderDetails    $order
     * @param CustomerDetails $customer
     * @param DateTime|null   $date
     * @param string          $content
     * @param bool            $createdByAdmin
     * @param DateTime|null   $createdOn
     */
    private function __construct(
        WithdrawalId $id,
        OrderDetails $order,
        CustomerDetails $customer,
        ?DateTime $date,
        string $content,
        bool $createdByAdmin,
        ?DateTime $createdOn = null
    ) {
        $this->id             = $id;
        $this->order          = $order;
        $this->customer       = $customer;
        $this->date           = $date;
        $this->content        = $content;
        $this->createdByAdmin = $createdByAdmin;
        $this->createdOn      = $createdOn;
    }
    
    
    /**
     * @param WithdrawalId    $id
     * @param OrderDetails    $order
     * @param CustomerDetails $customer
     * @param DateTime|null   $date
     * @param string          $content
     * @param bool            $createdByAdmin
     * @param DateTime|null   $createdOn
     *
     * @return Withdrawal
     */
    public static function create(
        WithdrawalId $id,
        OrderDetails $order,
        CustomerDetails $customer,
        ?DateTime $date,
        string $content,
        bool $createdByAdmin,
        ?DateTime $createdOn = null
    ): Withdrawal {
        return new self($id, $order, $customer, $date, $content, $createdByAdmin, $createdOn);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return int|null
     */
    public function orderId(): ?int
    {
        return $this->order->id();
    }
    
    
    /**
     * @return string|null
     */
    public function orderCreationDate(): ?string
    {
        return $this->order->creationDate();
    }
    
    
    /**
     * @return string|null
     */
    public function orderDeliveryDate(): ?string
    {
        return $this->order->deliveryDate();
    }
    
    
    /**
     * @return int|null
     */
    public function customerId(): ?int
    {
        return $this->customer->id();
    }
    
    
    /**
     * @return string
     */
    public function customerGender(): string
    {
        return $this->customer->gender();
    }
    
    
    /**
     * @return string
     */
    public function customerFirstName(): string
    {
        return $this->customer->firstName();
    }
    
    
    /**
     * @return string
     */
    public function customerLastName(): string
    {
        return $this->customer->lastName();
    }
    
    
    /**
     * @return string
     */
    public function customerStreet(): string
    {
        return $this->customer->street();
    }
    
    
    /**
     * @return string
     */
    public function customerPostcode(): string
    {
        return $this->customer->postcode();
    }
    
    
    /**
     * @return string
     */
    public function customerCity(): string
    {
        return $this->customer->city();
    }
    
    
    /**
     * @return string
     */
    public function customerCountry(): string
    {
        return $this->customer->country();
    }
    
    
    /**
     * @return string
     */
    public function customerEmail(): string
    {
        return $this->customer->email();
    }
    
    
    /**
     * @param string $format
     *
     * @return string|null
     */
    public function date(string $format = 'Y-m-d H:i:s'): ?string
    {
        return ($this->date !== null) ? $this->date->format($format) : null;
    }
    
    
    /**
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }
    
    
    /**
     * @return bool
     */
    public function wasCreatedByAdmin(): bool
    {
        return $this->createdByAdmin;
    }
    
    
    /**
     * @param string $format
     *
     * @return string|null
     */
    public function createdOn(string $format = 'Y-m-d H:i:s'): ?string
    {
        return ($this->createdOn !== null) ? $this->createdOn->format($format) : null;
    }
    
    
    /**
     * @param OrderId $orderId
     */
    public function changeOrderId(OrderId $orderId): void
    {
        $this->order->changeId($orderId);
        
        $this->raiseEvent(WithdrawalsOrderIdUpdated::create($this->id, $orderId));
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'             => $this->id(),
            'order'          => $this->order->toArray(),
            'customer'       => $this->customer->toArray(),
            'date'           => $this->date(),
            'content'        => $this->content(),
            'createdByAdmin' => $this->wasCreatedByAdmin(),
            'createdOn'      => $this->createdOn(),
        ];
    }
}