<?php
/* --------------------------------------------------------------
   OrderDetails.php 2020-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\ValueObjects;

use DateTime;

/**
 * Class OrderDetails
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\ValueObjects
 */
class OrderDetails
{
    /**
     * @var OrderId
     */
    private $id;
    
    /**
     * @var DateTime|null
     */
    private $creationDate;
    
    /**
     * @var DateTime|null
     */
    private $deliveryDate;
    
    
    /**
     * OrderDetails constructor.
     *
     * @param OrderId       $id
     * @param DateTime|null $creationDate
     * @param DateTime|null $deliveryDate
     */
    private function __construct(OrderId $id, ?DateTime $creationDate, ?DateTime $deliveryDate)
    {
        $this->id           = $id;
        $this->creationDate = $creationDate;
        $this->deliveryDate = $deliveryDate;
    }
    
    
    /**
     * @param OrderId       $id
     * @param DateTime|null $creationDate
     * @param DateTime|null $deliveryDate
     *
     * @return OrderDetails
     */
    public static function create(OrderId $id, ?DateTime $creationDate, ?DateTime $deliveryDate): OrderDetails
    {
        return new self($id, $creationDate, $deliveryDate);
    }
    
    
    /**
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->id->value();
    }
    
    
    /**
     * @param string $format
     *
     * @return string|null
     */
    public function creationDate(string $format = 'Y-m-d H:i:s'): ?string
    {
        return ($this->creationDate !== null) ? $this->creationDate->format($format) : null;
    }
    
    
    /**
     * @param string $format
     *
     * @return string|null
     */
    public function deliveryDate(string $format = 'Y-m-d H:i:s'): ?string
    {
        return ($this->deliveryDate !== null) ? $this->deliveryDate->format($format) : null;
    }
    
    
    /**
     * @param OrderId $orderId
     */
    public function changeId(OrderId $orderId): void
    {
        $this->id = $orderId;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->id(),
            'creationDate' => $this->creationDate(),
            'deliveryDate' => $this->deliveryDate(),
        ];
    }
}