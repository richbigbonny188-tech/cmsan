<?php
/*--------------------------------------------------------------
   CustomerMemo.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Model;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Events\CustomerMemosContentUpdated;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CreatorId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;
use Webmozart\Assert\Assert;

/**
 * Class CustomerMemo
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Model
 */
class CustomerMemo extends AbstractEventRaisingEntity
{
    private CustomerMemoId    $id;
    private string            $content;
    private CustomerId        $customerId;
    private CreatorId         $creatorId;
    private DateTimeImmutable $creationTime;
    private DateTimeImmutable $updatedAtTime;
    
    
    /**
     * CustomerMemo constructor.
     *
     * @param CustomerMemoId    $id
     * @param CustomerId        $customerId
     * @param CreatorId         $creatorId
     * @param string            $content
     * @param DateTimeImmutable $creationTime
     * @param DateTimeImmutable $updatedAtTime
     */
    private function __construct(
        CustomerMemoId    $id,
        CustomerId        $customerId,
        CreatorId         $creatorId,
        string            $content,
        DateTimeImmutable $creationTime,
        DateTimeImmutable $updatedAtTime
    ) {
        $this->id            = $id;
        $this->content       = $content;
        $this->customerId    = $customerId;
        $this->creatorId     = $creatorId;
        $this->creationTime  = $creationTime;
        $this->updatedAtTime = $updatedAtTime;
    }
    
    
    /**
     * Creates a new customer memo instance.
     *
     * @param CustomerMemoId    $id
     * @param CustomerId        $customerId
     * @param CreatorId         $creatorId
     * @param string            $content
     * @param DateTimeImmutable $creationTime
     * @param DateTimeImmutable $updatedAtTime
     *
     * @return CustomerMemo
     */
    public static function create(
        CustomerMemoId    $id,
        CustomerId        $customerId,
        CreatorId         $creatorId,
        string            $content,
        DateTimeImmutable $creationTime,
        DateTimeImmutable $updatedAtTime
    ): CustomerMemo {
        self::validateContent($content);
        
        return new self($id, $customerId, $creatorId, $content, $creationTime, $updatedAtTime);
    }
    
    
    /**
     * Returns the ID of the customer memo.
     *
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * Returns the content of the customer memo.
     *
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }
    
    
    /**
     * Returns the customer ID of the customer memo.
     *
     * @return int
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * Returns the customer/admin ID of the creator of the customer memo.
     *
     * @return int
     */
    public function creatorId(): int
    {
        return $this->creatorId->value();
    }
    
    
    /**
     * Returns the creation time of the customer memo.
     *
     * @param string $datetimeFormat
     *
     * @return string
     */
    public function creationTime(string $datetimeFormat = 'Y-m-d H:i:s'): string
    {
        return $this->creationTime->format($datetimeFormat);
    }
    
    
    /**
     * Returns the updated-at time of the customer memo.
     *
     * @param string $datetimeFormat
     *
     * @return string
     */
    public function updatedAtTime(string $datetimeFormat = 'Y-m-d H:i:s'): string
    {
        return $this->updatedAtTime->format($datetimeFormat);
    }
    
    
    /**
     * Updates the content of the customer memo.
     *
     * @param string $content
     */
    public function changeContent(string $content): void
    {
        self::validateContent($content);
        
        $this->content = $content;
        
        $this->raiseEvent(CustomerMemosContentUpdated::create($this->id, $content));
    }
    
    
    /**
     * Returns the internal data structure of the customer memo.
     *
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return [
            'id'            => $this->id(),
            'creatorId'     => $this->creatorId(),
            'content'       => $this->content(),
            'creationTime'  => $this->creationTime($datetimeFormat),
            'updatedAtTime' => $this->updatedAtTime($datetimeFormat),
        ];
    }
    
    
    /**
     * Validates the given content and throws an exception if it's invalid for using it for a customer memo.
     *
     * @param string $content
     */
    public static function validateContent(string $content): void
    {
        Assert::stringNotEmpty($content, 'The content of a customer memo can\'t be an empty string.');
    }
}