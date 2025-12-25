<?php
/*--------------------------------------------------------------
   ReviewDoesNotExistException.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects\ReviewId;

/**
 * Class ReviewDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\Exceptions
 */
class ReviewDoesNotExistException extends Exception
{
    /**
     * @param ReviewId $reviewId
     *
     * @return ReviewDoesNotExistException
     */
    final public static function forReviewId(ReviewId $reviewId): ReviewDoesNotExistException
    {
        $message = 'No review exist with the id "%s"';
        
        return new static(sprintf($message, $reviewId->value()), 0);
    }
}