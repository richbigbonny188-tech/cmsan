<?php

/* --------------------------------------------------------------
   AuthSecret.inc.php 2018-02-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a secret code value object
 */
class AuthSecret
{
    /**
     * Secret value
     * @var string
     */
    private $secret;
    
    
    /**
     * Return an instance with a randomly Base32-encoded value
     *
     * @param int $length Length of secret
     *
     * @return AuthSecret Instance
     */
    public static function byRandomValue($length = 16)
    {
        return new self((new TwoFactorAuthAuthenticator())->createSecret($length));
    }
    
    
    /**
     * Return an instance with the provided secret
     *
     * @param NonEmptyStringType $secret Secret
     *
     * @return AuthSecret Instance
     */
    public static function withCode(NonEmptyStringType $secret)
    {
        $code = $secret->asString();
        
        return new self($code);
    }
    
    
    /**
     * Return the secret value
     * @return string Secret value
     */
    public function code()
    {
        return $this->secret;
    }
    
    
    /**
     * Create instance
     *
     * @param string $secret Secret
     */
    private function __construct($secret)
    {
        $this->secret = $secret;
    }
}