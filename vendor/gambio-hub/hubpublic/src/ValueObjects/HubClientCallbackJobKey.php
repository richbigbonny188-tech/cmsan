<?php

/* --------------------------------------------------------------
   HubClientCallbackJobKey.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidHubClientCallbackJobKeyException;

/**
 * Class HubClientCallbackJobKey
 *
 * Represents a client callback job key
 *
 * @package HubPublic\ValueObjects
 */
class HubClientCallbackJobKey
{
    /**
     * Client callback job key value
     *
     * @var string
     */
    private $value;
    
    
    /**
     * HubClientCallbackJobKey constructor
     *
     * @param string $value Client callback job key value
     *
     * @throws \HubPublic\Exceptions\InvalidHubClientCallbackJobKeyException On invalid client callback job key
     */
    public function __construct(string $value)
    {
        if (!$this->formatIsValid($value)) {
            throw new InvalidHubClientCallbackJobKeyException('The given HubClientCallbackJobKey is not in a valid format. The entered key was: "'
                                                              . $value . '"');
        }
        
        $this->value = $value;
    }
    
    
    /**
     * Returns the client callback job key
     *
     * @return string Client callback job key
     */
    public function asString(): string
    {
        return $this->value;
    }
    
    
    /**
     * Checks if the HubClientCallbackJobKey is in the correct format.
     *
     * A 64 characters long  string with the format "GH-QK-[date]-[hash]-XX" whereas [date] with
     * format "YYYYMMDD" and [hash] as a random 46-character hexadecimal number.
     *
     * GH = Gambio Hub
     * QK = HubClientCallbackJobKey
     * XX = represents the end of the HubClientCallbackJobKey
     *
     * @param string $value The key that should be validated
     *
     * @return bool true if format is valid | false if format is invalid
     */
    private function formatIsValid(string $value): bool
    {
        return strlen($value) === 64 && preg_match('/GH-QK-\d{8}-[a-f0-9]{46}-XX/', $value) === 1;
    }
}
