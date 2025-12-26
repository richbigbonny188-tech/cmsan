<?php
/*--------------------------------------------------------------
   CustomerMemoReadService.php 2021-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\CustomerMemoDoesNotExistException;

/**
 * Interface CustomerMemoReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services
 */
interface CustomerMemoReadService
{
    /**
     * Returns all available customer memos.
     *
     * @param int $customerId
     *
     * @return CustomerMemos
     */
    public function getCustomerMemos(int $customerId): CustomerMemos;
    
    
    /**
     * Returns a specific customer memo based on the given ID.
     *
     * @param int $memoId
     *
     * @return CustomerMemo
     *
     * @throws CustomerMemoDoesNotExistException
     */
    public function getCustomerMemoById(int $memoId): CustomerMemo;
}