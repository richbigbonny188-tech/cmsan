<?php
/* --------------------------------------------------------------
   AuthHash.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidAuthHashException;

/**
 * Class CartContent
 *
 * @package HubPublic\ValueObjects
 */
class AuthHash
{
    /**
     * @var string
     */
    private $hash;
    
    
    /**
     * AuthHash constructor.
     *
     * @param string $hash authorization hash
     *
     * @throws \HubPublic\Exceptions\InvalidAuthHashException
     */
    public function __construct(string $hash)
    {
        if ($this->formatIsValid($hash) !== true) {
            throw new InvalidAuthHashException('The given authorization hash is not in a valid format. The entered key was: "'
                                               . $hash . '"');
        }
        
        $this->hash = $hash;
    }
    
    
    /**
     * Returns the authorization hash.
     * It is guaranteed that the hash is in a valid format.
     *
     * @return string Authorization hash
     */
    public function asString(): string
    {
        return $this->hash;
    }
    
    
    /**
     * Checks if the authorization hash is in the correct format. A 32 characters long hexadecimal string.
     *
     * @param string $hash The key that should be validated
     *
     * @return bool true if format is valid | false if format is invalid
     */
    private function formatIsValid(string $hash): bool
    {
        return strlen($hash) === 32 && ctype_xdigit($hash);
    }
}
