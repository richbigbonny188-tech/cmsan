<?php
/* --------------------------------------------------------------
   HubException.php 2016-11-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

use Exception;

/**
 * Class HubException
 *
 * Basic exception. All other exceptions of the hub project have to be an instance of this exception.
 *
 * @package HubPublic\Exceptions
 */
class HubException extends Exception
{
    /**
     * Returns the error code of the exception
     *
     * @return int
     */
    public function getErrorStatusCode(): int
    {
        return 500;
    }
}
