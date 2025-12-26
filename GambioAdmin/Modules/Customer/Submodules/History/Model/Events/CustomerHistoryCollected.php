<?php
/*--------------------------------------------------------------
   CustomerHistoryCollected.php 2023-06-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\Collections\CustomerHistoryEntryDtos;

/**
 * Class CustomerHistoryCollected
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Model\Events
 */
class CustomerHistoryCollected
{
    public function __construct(
        private CustomerId $id,
        private CustomerHistoryEntryDtos $dtos
    ) {
    }
    
    
    /**
     * @return CustomerId
     */
    public function id(): CustomerId
    {
        return $this->id;
    }
    
    
    /**
     * @return CustomerHistoryEntryDtos
     */
    public function dtos(): CustomerHistoryEntryDtos
    {
        return $this->dtos;
    }
}