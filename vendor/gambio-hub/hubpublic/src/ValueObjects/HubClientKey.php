<?php
/* --------------------------------------------------------------
   HubClientKey.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidHubClientKeyException;

/**
 * Class HubClientKey
 *
 * Represents a HubClientKey with 64 characters.
 *
 * @package HubPublic\ValueObjects
 */
class HubClientKey
{
    /**
     * Client key value
     *
     * @var string
     */
    private $value;
    
    
    /**
     * HubClientKey constructor.
     *
     * @param string $value Hub client key value
     *
     * @throws \HubPublic\Exceptions\InvalidHubClientKeyException If the given HubClientKey is not in a valid
     *                                                               format
     *
     */
    public function __construct(string $value)
    {
        if ($this->formatIsValid($value) !== true) {
            throw new InvalidHubClientKeyException('The given HubClientKey is not in a valid format. The entered key was: "'
                                                   . $value . '"');
        }
        
        $this->value = $value;
    }
    
    
    /**
     * Get the instance value as string.
     *
     * @return string HubClientKey value as string
     */
    public function asString(): string
    {
        return $this->value;
    }
    
    
    /**
     * Checks if the HubClientKey is in the correct format.
     *
     * A 64 characters long string with the format "GH-CK-[date]-[hash]-XX" whereas [date] with
     * format "YYYYMMDD" and [hash] as a random 46-character hexadecimal number.
     *
     * GH = Gambio Hub
     * CK = HubClientKey
     * XX = represents the end of the HubClientKey
     *
     * @param string $value The key that should be validated
     *
     * @return bool true if format is valid | false if format is invalid
     */
    private function formatIsValid(string $value): bool
    {
        return strlen($value) === 64 && preg_match('/GH-CK-\d{8}-[a-f0-9]{46}-XX/', $value) === 1;
    }
}
