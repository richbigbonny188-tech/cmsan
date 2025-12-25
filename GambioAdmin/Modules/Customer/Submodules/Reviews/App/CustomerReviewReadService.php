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

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\App;

use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\Collections\CustomerReviews;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\CustomerReview;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewFactory;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewReadService as CustomerReviewReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewRepository as CustomerReviewRepositoryInterface;

/**
 * Class CustomerReviewReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\App
 */
class CustomerReviewReadService implements CustomerReviewReadServiceInterface
{
    private CustomerReviewFactory             $factory;
    private CustomerReviewRepositoryInterface $repository;
    
    
    /**
     * @param CustomerReviewFactory             $factory
     * @param CustomerReviewRepositoryInterface $repository
     */
    public function __construct(
        CustomerReviewFactory             $factory,
        CustomerReviewRepositoryInterface $repository
    ) {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerReviews(int $customerId): CustomerReviews
    {
        return $this->repository->getCustomerReviews($this->factory->createCustomerId($customerId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerReviewByReviewId(int $reviewId): CustomerReview
    {
        return $this->repository->getCustomerReviewByReviewId($this->factory->createReviewId($reviewId));
    }
}