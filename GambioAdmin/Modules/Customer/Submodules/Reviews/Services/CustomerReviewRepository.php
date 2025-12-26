<?php
/*--------------------------------------------------------------
   CustomerReviewRepository.php 2022-09-28
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
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\Exceptions\ReviewDoesNotExistException;

/**
 * Interface CustomerReviewRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Services
 */
interface CustomerReviewRepository
{
    /**
     * Returns all available customer reviews.
     *
     * @param CustomerId $customerId
     *
     * @return CustomerReviews
     * @throws ReviewDoesNotExistException
     */
    public function getCustomerReviews(CustomerId $customerId): CustomerReviews;
    
    
    /**
     * Returns a specific customer review based on the given ID.
     *
     * @param ReviewId $reviewId
     *
     * @return CustomerReview
     * @throws ReviewDoesNotExistException
     */
    public function getCustomerReviewByReviewId(ReviewId $reviewId): CustomerReview;
}