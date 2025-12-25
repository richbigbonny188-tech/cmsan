<?php

/* --------------------------------------------------------------
   HubTransactionCode.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidHubTransactionCodeException;

/**
 * Class HubTransactionCode
 *
 * Represents a HubTransactionCode with 64 characters.
 *
 * @package HubPublic\ValueObjects
 */
class HubTransactionCode
{
    /**
     * Hub transaction code value
     *
     * @var string
     */
    private $value;
    
    
    /**
     * HubTransactionCode constructor.
     *
     * @param string $value Hub transaction code value
     *
     * @throws \HubPublic\Exceptions\InvalidHubTransactionCodeException If given HubTransactionCode is not in a
     *                                                                     valid format
     *
     */
    public function __construct(string $value)
    {
        if ($this->formatIsValid($value) !== true) {
            throw new InvalidHubTransactionCodeException('The given HubTransactionCode is not in a valid format. The entered code was: "'
                                                         . $value . '"');
        }
        
        $this->value = $value;
    }
    
    
    /**
     * Get the instance value as string.
     *
     * @return string HubTransactionCode value as string
     */
    public function asString(): string
    {
        return $this->value;
    }
    
    
    /**
     * Checks if the HubTransactionCode is in the correct format.
     *
     * A 64 characters long string with the format "GH-TX-[date]-[hash]-XX" whereas [date] with
     * format "YYYYMMDD" and [hash] as a random 46-character hexadecimal number.
     *
     * GH = Gambio Hub
     * TX = HubTransactionCode
     * XX = represents the end of the HubTransactionCode
     *
     * @param string $value $value The key that should be validated
     *
     * @return bool true if format is valid | false if format is invalid
     */
    private function formatIsValid(string $value): bool
    {
        return strlen($value) === 64 && preg_match('/GH-TX-\d{8}-[a-f0-9]{46}-XX/', $value) === 1;
    }
}
