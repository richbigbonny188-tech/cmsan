<?php
/*--------------------------------------------------------------
   DeletionOfAdditionalProductFieldsFailedException.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions;

use Exception;

/**
 * Class DeletionOfAdditionalProductFieldsFailedException
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions
 */
class DeletionOfAdditionalProductFieldsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfAdditionalProductFieldsFailedException
     */
    public static function becauseOfException(Exception $exception): DeletionOfAdditionalProductFieldsFailedException
    {
        $message = 'Deletion of additional product field failed because of a "%s" with the message "%s"';
        $message = sprintf($message, get_class($exception), $exception->getMessage());
        
        return new static($message, 0, $exception);
    }
}