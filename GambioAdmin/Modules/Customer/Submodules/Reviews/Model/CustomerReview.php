<?php
/*--------------------------------------------------------------
   CustomerReview.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\Model;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\ReviewId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\ReviewText;
use Webmozart\Assert\Assert;

/**
 * Class CustomerReview
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Model
 */
class CustomerReview
{
    private ReviewId          $reviewId;
    private CustomerId        $customerId;
    private int               $productId;
    private ReviewText        $text;
    private DateTimeImmutable $creationTime;
    private int               $rating;
    
    
    /**
     * @param ReviewId          $reviewId
     * @param CustomerId        $customerId
     * @param int               $productId
     * @param ReviewText        $text
     * @param DateTimeImmutable $creationTime
     * @param int               $rating
     */
    private function __construct(
        ReviewId          $reviewId,
        CustomerId        $customerId,
        int               $productId,
        ReviewText        $text,
        DateTimeImmutable $creationTime,
        int               $rating
    ) {
        $this->reviewId     = $reviewId;
        $this->customerId   = $customerId;
        $this->productId    = $productId;
        $this->text         = $text;
        $this->creationTime = $creationTime;
        $this->rating       = $rating;
    }
    
    
    /**
     * @param ReviewId          $reviewId
     * @param CustomerId        $customerId
     * @param int               $productId
     * @param ReviewText        $text
     * @param DateTimeImmutable $creationTime
     * @param int               $rating
     *
     * @return CustomerReview
     */
    public static function create(
        ReviewId          $reviewId,
        CustomerId        $customerId,
        int               $productId,
        ReviewText        $text,
        DateTimeImmutable $creationTime,
        int               $rating
    ): CustomerReview {
        
        Assert::range($rating, 1, 5);
        Assert::greaterThan($productId, 0);
        
        return new self($reviewId, $customerId, $productId, $text, $creationTime, $rating);
    }
    
    
    /**
     * @return int
     */
    public function reviewId(): int
    {
        return $this->reviewId->value();
    }
    
    
    /**
     * @return int
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * @return int
     */
    public function productId(): int
    {
        return $this->productId;
    }
    
    
    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text->text();
    }
    
    
    /**
     * @return string
     */
    public function code(): string
    {
        return $this->text->code();
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
     * @return int
     */
    public function rating(): int
    {
        return $this->rating;
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return [
            'reviewId'     => $this->reviewId(),
            'customerId'   => $this->customerId(),
            'productId'    => $this->productId(),
            'text'         => $this->text(),
            'code'         => $this->code(),
            'creationTime' => $this->creationTime($datetimeFormat),
            'rating'       => $this->rating(),
        ];
    }
}