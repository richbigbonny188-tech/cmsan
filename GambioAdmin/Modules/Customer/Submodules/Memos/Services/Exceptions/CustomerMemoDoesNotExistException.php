<?php
/*--------------------------------------------------------------
   CustomerMemoDoesNotExistException.php 2021-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;

/**
 * Class CustomerMemoDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions
 */
class CustomerMemoDoesNotExistException extends Exception
{
    /**
     * @param CustomerMemoId $memoId
     *
     * @return CustomerMemoDoesNotExistException
     */
    final public static function forCustomerMemoId(CustomerMemoId $memoId): CustomerMemoDoesNotExistException
    {
        $message = 'No customer memo was found with the id "%s"';
        
        return new static(sprintf($message, $memoId->value()), 1);
    }
}