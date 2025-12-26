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

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\App;

use Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data\ReviewMapper;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data\ReviewReader;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\Collections\CustomerReviews;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\CustomerReview;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\ReviewId;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewRepository as CustomerReviewRepositoryInterface;

/**
 * Class CustomerReviewRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\App
 */
class CustomerReviewRepository implements CustomerReviewRepositoryInterface
{
    private ReviewReader $reader;
    private ReviewMapper $mapper;
    
    
    /**
     * @param ReviewReader $reader
     * @param ReviewMapper $mapper
     */
    public function __construct(
        ReviewReader $reader,
        ReviewMapper $mapper
    ) {
        $this->reader = $reader;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerReviews(CustomerId $customerId): CustomerReviews
    {
        $data = $this->reader->getCustomerReviews($customerId);
        
        return $this->mapper->mapCustomerReviews(...$data);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerReviewByReviewId(ReviewId $reviewId): CustomerReview
    {
        $data = $this->reader->getCustomerReviewByReviewId($reviewId);
        
        return $this->mapper->mapCustomerReview(array_shift($data));
    }
}