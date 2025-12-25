<?php
/* --------------------------------------------------------------
   HubOrderCode.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidHubOrderCodeException;

/**
 * Class HubOrderCode
 *
 * @package HubPublic\ValueObjects
 */
class HubOrderCode
{
    /**
     * Order code value
     *
     * @var string
     */
    private $value;
    
    
    /**
     * HubOrderCode constructor.
     *
     * @param string $value Hub order code value
     *
     * @throws \HubPublic\Exceptions\InvalidHubOrderCodeException If the given HubOrderCode is not in a valid
     *                                                               format
     *
     */
    public function __construct(string $value)
    {
        if ($this->formatIsValid($value) !== true) {
            throw new InvalidHubOrderCodeException('The given HubOrderCode is not in a valid format. The entered key was: "'
                                                   . $value . '"');
        }
        
        $this->value = $value;
    }
    
    
    /**
     * Get the instance value as string.
     *
     * @return string HubOrderCode value as string
     */
    public function asString(): string
    {
        return $this->value;
    }
    
    
    /**
     * Checks if the HubOrderCode is in the correct format.
     *
     * A 64 characters long string with the format "GH-OC-[date]-[hash]-XX" whereas [date] with
     * format "YYYYMMDD" and [hash] as a random 46-character hexadecimal number.
     *
     * GH = Gambio Hub
     * CK = HubOrderCode
     * XX = represents the end of the HubOrderCode
     *
     * @param string $value The key that should be validated
     *
     * @return bool true if format is valid | false if format is invalid
     */
    private function formatIsValid(string $value): bool
    {
        return strlen($value) === 64 && preg_match('/GH-OC-\d{8}-[a-f0-9]{46}-XX/', $value) === 1;
    }
}
