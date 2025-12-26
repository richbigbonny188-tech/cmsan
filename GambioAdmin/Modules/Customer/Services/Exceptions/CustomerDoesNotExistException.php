<?php
/*--------------------------------------------------------------
   CustomerDoesNotExistException.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;

/**
 * Class CustomerDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class CustomerDoesNotExistException extends Exception
{
    /**
     * @param CustomerId $id
     *
     * @return CustomerDoesNotExistException
     */
    public static function forId(CustomerId $id): CustomerDoesNotExistException
    {
        return new static(sprintf('Customer with ID "%s" does not exists.', $id->value()), 1);
    }
}