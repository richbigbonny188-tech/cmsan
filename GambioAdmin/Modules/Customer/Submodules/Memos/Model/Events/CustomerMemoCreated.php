<?php
/*--------------------------------------------------------------
   CustomerMemoCreated.php 2021-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;

/**
 * Class CustomerMemoCreated
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Events
 * @codeCoverageIgnore
 */
class CustomerMemoCreated
{
    private CustomerMemoId $customerMemoId;
    
    
    /**
     * @param CustomerMemoId $customerMemoId
     */
    private function __construct(CustomerMemoId $customerMemoId)
    {
        $this->customerMemoId = $customerMemoId;
    }
    
    
    /**
     * @param CustomerMemoId $customerMemoId
     *
     * @return CustomerMemoCreated
     */
    public static function create(CustomerMemoId $customerMemoId): CustomerMemoCreated
    {
        return new self($customerMemoId);
    }
    
    
    /**
     * @return CustomerMemoId
     */
    public function customerMemoId(): CustomerMemoId
    {
        return $this->customerMemoId;
    }
}