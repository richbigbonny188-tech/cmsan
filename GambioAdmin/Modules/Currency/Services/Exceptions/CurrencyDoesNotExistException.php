<?php
/*--------------------------------------------------------------
   CurrencyDoesNotExistException.php 2022-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Currency\Model\ValueObjects\CurrencyId;

/**
 * Class CurrencyDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Currency\Services\Exceptions
 */
class CurrencyDoesNotExistException extends Exception
{
    /**
     * @param CurrencyId $id
     *
     * @return CurrencyDoesNotExistException
     */
    public static function forCurrencyId(CurrencyId $id): CurrencyDoesNotExistException
    {
        $message = 'No currency exists with the ID "%s"';
        
        return new self(sprintf($message, $id->value()), 1);
    }
}