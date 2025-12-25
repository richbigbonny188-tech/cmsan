<?php
/* --------------------------------------------------------------
   GeschaeftskundenversandValidationFailedException.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);


class GeschaeftskundenversandValidationFailedException extends Exception
{
    protected array $validationMessages = [];
    protected bool  $isError = false;
    
    
    public static function withMessages(string ...$messages): GeschaeftskundenversandValidationFailedException
    {
        $instance = new static('Validation failed');
        $instance->setValidationMessages($messages);
        return $instance;
    }
    
    public static function warningWithMessages(string ...$messages): GeschaeftskundenversandValidationFailedException
    {
        $instance = new static('Validation failed (soft)');
        $instance->setValidationMessages($messages);
        $instance->setIsError(false);
        return $instance;
    }

    public static function errorWithMessages(string ...$messages): GeschaeftskundenversandValidationFailedException
    {
        $instance = new static('Validation failed (hard)');
        $instance->setValidationMessages($messages);
        $instance->setIsError(true);
        return $instance;
    }
    
    protected function setValidationMessages(array $messages)
    {
        $this->validationMessages = $messages;
    }
    
    
    /**
     * @return array
     */
    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }
    
    
    protected function setIsError(bool $isError): void
    {
        $this->isError = $isError;
    }
    
    
    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->isError;
    }
    
    
}