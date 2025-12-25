<?php
/*--------------------------------------------------------------
   CustomerHistoryReader.php 2022-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Services;

use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\Collections\CustomerHistoryEntryDtos;

/**
 * Interface CustomerHistoryReader
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Services
 */
interface CustomerHistoryReader
{
    /**
     * Returns all available customer history entries.
     *
     * @param CustomerId $id
     *
     * @return CustomerHistoryEntryDtos
     */
    public function getCustomerHistoryEntries(CustomerId $id): CustomerHistoryEntryDtos;
    
    
    /**
     * Returns type of history entry the reader provides
     *
     * @return string
     */
    public function getType(): string;
}