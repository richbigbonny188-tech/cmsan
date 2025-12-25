<?php
/*--------------------------------------------------------------
   CustomerReviews.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\CustomerReview;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerReviews
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\Collections
 */
class CustomerReviews implements IteratorAggregate
{
    /**
     * @var CustomerReview[]
     */
    private array $customerReviews;
    
    
    /**
     * CustomerReviews constructor.
     *
     * @param CustomerReview[] $customerReviews
     */
    private function __construct(array $customerReviews)
    {
        $this->customerReviews = $customerReviews;
    }
    
    
    /**
     * @param CustomerReview ...$customerReviews
     *
     * @return CustomerReviews
     */
    public static function create(CustomerReview ...$customerReviews): CustomerReviews
    {
        return new self($customerReviews);
    }
    
    
    /**
     * @return Traversable|CustomerReview[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customerReviews);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return array_map(fn(CustomerReview $review): array => $review->toArray($datetimeFormat), $this->customerReviews);
    }
}