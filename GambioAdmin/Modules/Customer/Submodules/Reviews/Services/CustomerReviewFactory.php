<?php
/*--------------------------------------------------------------
   CustomerReviewFactory.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\Services;

use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\Collections\CustomerReviews;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\CustomerReview;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\ReviewId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\ReviewText;

/**
 * Class CustomerReviewFactory
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Services
 */
class CustomerReviewFactory
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
     * @param int $reviewId
     *
     * @return ReviewId
     */
    public function createReviewId(int $reviewId): ReviewId
    {
        return ReviewId::create($reviewId);
    }
    
    
    /**
     * @param string $code
     * @param string $text
     *
     * @return ReviewText
     */
    public function createReviewText(string $code, string $text): ReviewText
    {
        return ReviewText::create($code, $text);
    }
    
    
    /**
     * @param CustomerReview ...$reviews
     *
     * @return CustomerReviews
     */
    public function createCustomerReviews(CustomerReview ...$reviews): CustomerReviews
    {
        return CustomerReviews::create(...$reviews);
    }
}