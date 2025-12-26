<?php
/*--------------------------------------------------------------
   ReviewMapper.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\Collections\CustomerReviews;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\CustomerReview;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewFactory;

/**
 * Class ReviewMapper
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data
 */
class ReviewMapper extends CustomerReviewFactory
{
    /**
     * @param array $data
     *
     * @return CustomerReview
     */
    public function mapCustomerReview(array $data): CustomerReview
    {
        $reviewId     = $this->createReviewId((int)$data['reviews_id']);
        $customerId   = $this->createCustomerId((int)$data['customers_id']);
        $productId    = (int)$data['products_id'];
        $text         = $this->createReviewText($data['code'], $data['reviews_text']);
        $creationTime = new DateTimeImmutable($data['date_added']);
        $rating       = (int)$data['reviews_rating'];
        
        return CustomerReview::create($reviewId, $customerId, $productId, $text, $creationTime, $rating);
    }
    
    
    /**
     * @param array ...$data
     *
     * @return CustomerReviews
     */
    public function mapCustomerReviews(array ...$data): CustomerReviews
    {
        $reviews = array_map([$this, 'mapCustomerReview'], $data);
        
        return $this->createCustomerReviews(...$reviews);
    }
}