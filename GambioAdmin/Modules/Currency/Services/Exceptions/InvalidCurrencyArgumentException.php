<?php
/*--------------------------------------------------------------
   InvalidCurrencyArgumentException.php 2022-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Services\Exceptions;

use Exception;
use InvalidArgumentException;

/**
 * Class InvalidCurrencyArgumentException
 *
 * @package Gambio\Admin\Modules\Currency\Services\Exceptions
 */
class InvalidCurrencyArgumentException extends Exception
{
    /**
     * @param InvalidArgumentException $exception
     *
     * @return InvalidCurrencyArgumentException
     */
    public static function createdFromPrevious(InvalidArgumentException $exception): InvalidCurrencyArgumentException
    {
        return new self($exception->getMessage(), 0, $exception);
    }
}