<?php

/* --------------------------------------------------------------
   AuthToken.inc.php 2018-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a token code value object
 */
class AuthToken
{
    /**
     * Valid token length
     */
    const EXPECTED_LENGTH = 6;
    
    /**
     * Token value
     * @var string
     */
    private $token;
    
    
    /**
     * Create an instance with the provided token
     *
     * @param NonEmptyStringType $token Token
     *
     * @return AuthToken Instance
     */
    public static function withCode(NonEmptyStringType $token)
    {
        $code = $token->asString();
        
        return new self($code);
    }
    
    
    /**
     * Return the token value
     * @return string token value
     */
    public function code()
    {
        return $this->token;
    }
    
    
    /**
     * Create instance
     *
     * @param string $token Token
     *
     * @throws AuthTokenLengthException On invalid length
     * @throws AuthTokenFormatException On invalid format
     */
    private function __construct($token)
    {
        if (strlen($token) !== self::EXPECTED_LENGTH) {
            throw new AuthTokenLengthException('Invalid length');
        }
        
        if (!is_numeric($token)) {
            throw new AuthTokenFormatException('Invalid format');
        }
        
        $this->token = $token;
    }
}