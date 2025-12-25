<?php
/*--------------------------------------------------------------
   CreationOfAdditionalProductFieldFailedException.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions;

use Exception;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\ProductId;

/**
 * Class CreationOfAdditionalProductFieldFailedException
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions
 */
class CreationOfAdditionalProductFieldFailedException extends Exception
{
    /**
     * @return CreationOfAdditionalProductFieldFailedException
     */
    public static function invalidLanguageCodesProvided(): CreationOfAdditionalProductFieldFailedException
    {
        $message = 'Some provided language keys where unresolvable';
        
        return new static($message, 4);
    }
    
    /**
     * @param ProductId $productId
     *
     * @return CreationOfAdditionalProductFieldFailedException
     */
    public static function productDoesNotExist(ProductId $productId): CreationOfAdditionalProductFieldFailedException
    {
        $message = 'No product with the id "%s" exists';
        $message = sprintf($message, $productId->value());
        
        return new static($message, 3);
    }
    
    /**
     * @param AdditionalFieldId $fieldId
     *
     * @return CreationOfAdditionalProductFieldFailedException
     */
    public static function fieldIdDoesNotExists(AdditionalFieldId $fieldId): CreationOfAdditionalProductFieldFailedException
    {
        $message = 'No additional field with the id "%s" exists';
        $message = sprintf($message, $fieldId->value());
        
        return new static($message, 2);
    }
    
    /**
     * @param ProductId         $productId
     * @param AdditionalFieldId $fieldId
     *
     * @return CreationOfAdditionalProductFieldFailedException
     */
    public static function valueAlreadyExists(ProductId $productId, AdditionalFieldId $fieldId): CreationOfAdditionalProductFieldFailedException
    {
        $message = 'There are already values assigned to the field id "%s" for the product id "%s"';
        $message = sprintf($message, $fieldId->value(), $productId->value());
        
        return new static($message, 1);
    }
    
    /**
     * @param Exception $exception
     *
     * @return CreationOfAdditionalProductFieldFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfAdditionalProductFieldFailedException
    {
        $message = 'Creation of additional field failed because of a %s with the message "%s"';
        $message = sprintf($message, get_class($exception), $exception->getMessage());
        
        return new static($message, 0, $exception);
    }
}