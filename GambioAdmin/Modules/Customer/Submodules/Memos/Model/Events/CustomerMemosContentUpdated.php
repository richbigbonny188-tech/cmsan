<?php
/*--------------------------------------------------------------
   CustomerMemosContentUpdated.php 2022-09-14
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
 * Class CustomerMemosContentUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Events
 * @codeCoverageIgnore
 */
class CustomerMemosContentUpdated
{
    private CustomerMemoId $customerMemoId;
    private string         $content;
    
    
    /**
     * @param CustomerMemoId $customerMemoId
     * @param string         $content
     */
    private function __construct(CustomerMemoId $customerMemoId, string $content)
    {
        $this->customerMemoId = $customerMemoId;
        $this->content        = $content;
    }
    
    
    /**
     * @param CustomerMemoId $customerMemoId
     * @param string         $content
     *
     * @return CustomerMemosContentUpdated
     */
    public static function create(CustomerMemoId $customerMemoId, string $content): CustomerMemosContentUpdated
    {
        return new self($customerMemoId, $content);
    }
    
    
    /**
     * @return CustomerMemoId
     */
    public function customerMemoId(): CustomerMemoId
    {
        return $this->customerMemoId;
    }
    
    
    /**
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }
}