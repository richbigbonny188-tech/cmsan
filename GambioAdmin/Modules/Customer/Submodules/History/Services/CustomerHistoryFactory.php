<?php
/*--------------------------------------------------------------
   CustomerHistoryFactory.php 2022-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Services;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections\CustomerHistory;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\CustomerHistoryEntry;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\Collections\CustomerHistoryEntryDtos;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\CustomerHistoryEntryDto;

/**
 * Class CustomerHistoryFactory
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Services
 */
class CustomerHistoryFactory
{
    /**
     * @param int $customerId
     *
     * @return CustomerId
     */
    public function createCustomerId(int $customerId): CustomerId
    {
        return CustomerId::create($customerId);
    }
    
    
    /**
     * @param CustomerHistoryEntry ...$entries
     *
     * @return CustomerHistory
     */
    public function createCustomerHistory(CustomerHistoryEntry ...$entries): CustomerHistory
    {
        return CustomerHistory::create(...$entries);
    }
    
    
    /**
     * @param int               $customerId
     * @param array             $payload
     * @param string            $type
     * @param DateTimeImmutable $date
     *
     * @return CustomerHistoryEntryDto
     */
    public function createCustomerHistoryEntryDto(
        int               $customerId,
        array             $payload,
        string            $type,
        DateTimeImmutable $date
    ): CustomerHistoryEntryDto {
        
        return CustomerHistoryEntryDto::create($customerId,
                                               $payload,
                                               $type,
                                               $date);
    }
    
    
    /**
     * @param CustomerHistoryEntryDto ...$dtos
     *
     * @return CustomerHistoryEntryDtos
     */
    public function createCustomerHistoryEntryDtos(CustomerHistoryEntryDto ...$dtos): CustomerHistoryEntryDtos
    {
        return CustomerHistoryEntryDtos::create(...$dtos);
    }
}