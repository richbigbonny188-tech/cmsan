<?php
/*--------------------------------------------------------------
   CustomerReviewReadService.php 2022-09-28
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
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\Exceptions\ReviewDoesNotExistException;

/**
 * Interface CustomerReviewReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Services
 */
interface CustomerReviewReadService
{
    /**
     * Returns all available customer reviews.
     *
     * @param int      $customerId
     *
     * @return CustomerReviews
     * @throws ReviewDoesNotExistException
     */
    public function getCustomerReviews(int $customerId): CustomerReviews;
    
    
    /**
     * Returns a specific customer review based on the given ID.
     *
     * @param int $reviewId
     *
     * @return CustomerReview
     * @throws ReviewDoesNotExistException
     */
    public function getCustomerReviewByReviewId(int $reviewId): CustomerReview;
}