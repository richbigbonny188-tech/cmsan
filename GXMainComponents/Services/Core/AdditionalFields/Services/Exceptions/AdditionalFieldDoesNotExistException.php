<?php
/*--------------------------------------------------------------
   AdditionalFieldDoesNotExistException.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions;

use Exception;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;

/**
 * Class AdditionalFieldDoesNotExistException
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions
 */
class AdditionalFieldDoesNotExistException extends Exception
{
    /**
     * @param FieldId $fieldId
     *
     * @return AdditionalFieldDoesNotExistException
     */
    public static function forFieldId(FieldId $fieldId): AdditionalFieldDoesNotExistException
    {
        $message = 'Additional field with ID "%s" does not exist.';
        $message = sprintf($message, $fieldId->value());
        
        return new static($message, 1);
    }
}