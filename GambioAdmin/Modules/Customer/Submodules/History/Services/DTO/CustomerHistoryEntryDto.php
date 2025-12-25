<?php
/*--------------------------------------------------------------
   CustomerHistoryEntryDto.php 2022-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO;

use DateTimeImmutable;

/**
 * Class CustomerHistoryEntryDto
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO
 * @codeCoverageIgnore
 */
class CustomerHistoryEntryDto
{
    private int               $customerId;
    private array             $payload;
    private string            $type;
    private DateTimeImmutable $date;
    
    
    /**
     * @param int               $customerId
     * @param array             $payload
     * @param string            $type
     * @param DateTimeImmutable $date
     */
    private function __construct(
        int               $customerId,
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
     * @param int               $customerId
     * @param array             $payload
     * @param string            $type
     * @param DateTimeImmutable $date
     *
     * @return CustomerHistoryEntryDto
     */
    public static function create(
        int               $customerId,
        array             $payload,
        string            $type,
        DateTimeImmutable $date
    ): CustomerHistoryEntryDto {
        
        return new self($customerId, $payload, $type, $date);
    }
    
    
    /**
     * @return int
     */
    public function customerId(): int
    {
        return $this->customerId;
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
     * @return DateTimeImmutable
     */
    public function date(): DateTimeImmutable
    {
        return $this->date;
    }
}