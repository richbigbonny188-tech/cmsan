<?php
/*--------------------------------------------------------------
   CustomerMemoMapper.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data;

use DateTimeImmutable;
use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;

/**
 * Class CustomerMemoMapper
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data
 */
class CustomerMemoMapper extends CustomerMemoFactory
{
    /**
     * @param array ...$customerMemos
     *
     * @return CustomerMemos
     * @throws Exception
     */
    public function mapCustomerMemos(array ...$customerMemos): CustomerMemos
    {
        return $this->createCustomerMemos(...array_map([$this, 'mapCustomerMemo'], $customerMemos));
    }
    
    
    /**
     * @throws Exception
     */
    public function mapCustomerMemo(array $customerMemo): CustomerMemo
    {
        return CustomerMemo::create($this->createCustomerMemoId((int)$customerMemo['memo_id']),
                                    $this->createCustomerId((int)$customerMemo['customers_id']),
                                    $this->createCreatorId((int)$customerMemo['poster_id']),
                                    $customerMemo['memo_text'],
                                    new DateTimeImmutable($customerMemo['memo_date']),
                                    new DateTimeImmutable($customerMemo['last_modified']));
    }
}