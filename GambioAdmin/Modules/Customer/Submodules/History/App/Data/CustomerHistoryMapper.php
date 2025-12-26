<?php
/*--------------------------------------------------------------
   CustomerHistoryMapper.php 2022-07-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\App\Data;

use Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections\CustomerHistory;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\CustomerHistoryEntry;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryFactory;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\Collections\CustomerHistoryEntryDtos;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\CustomerHistoryEntryDto;

/**
 * Class CustomerHistoryMapper
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\App\Data
 */
class CustomerHistoryMapper extends CustomerHistoryFactory
{
    /**
     * @param CustomerHistoryEntryDtos $dtos
     *
     * @return CustomerHistory
     */
    public function mapCustomerHistory(CustomerHistoryEntryDtos $dtos): CustomerHistory
    {
        return $this->createCustomerHistory(...array_map([$this, 'mapCustomerHistoryEntry'], $dtos->getArray()));
    }
    
    
    /**
     * @param CustomerHistoryEntryDto $dto
     *
     * @return CustomerHistoryEntry
     */
    public function mapCustomerHistoryEntry(CustomerHistoryEntryDto $dto): CustomerHistoryEntry
    {
        $id      = $this->createCustomerId($dto->customerId());
        $payload = $this->typeCastPayload($dto->payload());
        
        return CustomerHistoryEntry::create($id, $payload, $dto->type(), $dto->date());
    }
    
    
    /**
     * @param array $payload
     *
     * @return array
     */
    private function typeCastPayload(array $payload): array
    {
        $intColumns = ['reviews_id', 'products_id', 'reviews_rating',];
    
        foreach ($intColumns as $column) {
    
            if (isset($payload[$column]) === true) {
        
                $payload[$column] = (int)$payload[$column];
            }
        }
        
        if (isset($payload['quantity']) === true) {
            
            $payload['quantity'] = (float)$payload['quantity'];
        }
        
        return $payload;
    }
}