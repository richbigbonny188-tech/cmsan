<?php
/*--------------------------------------------------------------
   CustomerHistoryEntry.php 2022-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Model;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;
use Webmozart\Assert\Assert;

/**
 * Class CustomerHistoryEntry
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Model
 */
class CustomerHistoryEntry extends AbstractEventRaisingEntity
{
    private CustomerId        $customerId;
    private array             $payload;
    private string            $type;
    private DateTimeImmutable $date;
    
    
    /**
     * @param CustomerId        $customerId
     * @param array             $payload
     * @param string            $type
     * @param DateTimeImmutable $date
     */
    private function __construct(
        CustomerId        $customerId,
        array             $payload,
        string            $type,
        DateTimeImmutable $date
    ) {
        $this->customerId = $customerId;
        $this->payload    = $payload;
        $this->type       = $type;
        $this->date       = $date;
    }
    
    
    /**
     * @param CustomerId        $customerId
     * @param array             $payload
     * @param string            $type
     * @param DateTimeImmutable $date
     *
     * @return CustomerHistoryEntry
     */
    public static function create(
        CustomerId        $customerId,
        array             $payload,
        string            $type,
        DateTimeImmutable $date
    ): CustomerHistoryEntry {
        
        Assert::isNonEmptyMap($payload, 'Payload must be a non empty map');
        Assert::stringNotEmpty($type, 'Type must be a non empty string');
        
        return new self($customerId, $payload, $type, $date);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return [
            'customerId' => $this->customerId(),
            'payload'    => $this->payload(),
            'type'       => $this->type(),
            'date'       => $this->date($datetimeFormat),
        ];
    }
    
    
    /**
     * @return int
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }
    
    
    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return string
     */
    public function date(string $datetimeFormat = 'Y-m-d H:i:s'): string
    {
        return $this->date->format($datetimeFormat);
    }
}