<?php
/*--------------------------------------------------------------
   CreationOfAdditionalFieldFailedException.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions;

use Exception;

/**
 * Class CreationOfAdditionalFieldFailedException
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions
 */
class CreationOfAdditionalFieldFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfAdditionalFieldFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfAdditionalFieldFailedException
    {
        $message = 'Creation of additional field failed because of a %s';
        $message = sprintf($message, get_class($exception));
        
        return new static($message, 0, $exception);
    }
}