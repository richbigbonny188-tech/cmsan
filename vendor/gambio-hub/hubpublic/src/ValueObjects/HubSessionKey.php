<?php
/* --------------------------------------------------------------
   HubSessionKey.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidHubSessionKeyException;

/**
 * Class HubSessionKey
 *
 * Represents a HubSessionKey with 64 characters.
 *
 * @package HubPublic\ValueObjects
 */
class HubSessionKey
{
    /**
     * Session key value
     *
     * @var string
     */
    private $value;
    
    
    /**
     * HubSessionKey constructor.
     *
     * @param string $value Session key value
     *
     * @throws \HubPublic\Exceptions\InvalidHubSessionKeyException If given HubSessionKey is in an invalid format
     *
     */
    public function __construct(string $value)
    {
        if ($this->formatIsValid($value) !== true) {
            throw new InvalidHubSessionKeyException('The given HubSessionKey is not in a valid format. The entered key was: "'
                                                    . $value . '"');
        }
        
        $this->value = $value;
    }
    
    
    /**
     * Get the instance value as string.
     *
     * @return string HubSessionKey value as string
     */
    public function asString(): string
    {
        return $this->value;
    }
    
    
    /**
     * Checks if the HubSessionKey is in the correct format.
     *
     * A 64 characters long  string with the format "GH-SK-[date]-[hash]-XX" whereas [date] with
     * format "YYYYMMDD" and [hash] as a random 46-character hexadecimal number.
     *
     * GH = Gambio Hub
     * CK = HubSessionKey
     * XX = represents the end of the HubSessionKey
     *
     * @param string $value The key that should be validated
     *
     * @return bool true if format is valid | false if format is invalid
     */
    private function formatIsValid(string $value): bool
    {
        return strlen($value) === 64 && preg_match('/GH-SK-\d{8}-[a-f0-9]{46}-XX/', $value) === 1;
    }
}
