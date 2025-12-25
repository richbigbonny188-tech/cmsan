<?php

/* --------------------------------------------------------------
   AuthServiceInterface.inc.php 2016-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AuthServiceInterface
 *
 * @category   System
 * @package    Authentication
 * @subpackage Interfaces
 */
interface AuthServiceInterface
{
    /**
     * Authenticates a user by its email and password stored in the given AuthCredentialsInterface object.
     *
     * @param AuthCredentialsInterface $authCredentials .
     *
     * @return bool Returns the result of the authentication.
     */
    public function authUser(AuthCredentialsInterface $authCredentials);
    
    
    /**
     * Verifies a password by its hash.
     *
     * @param StringType         $password Password.
     * @param NonEmptyStringType $hash     Hash.
     *
     * @return bool Returns the result of the verification.
     */
    public function verify(StringType $password, NonEmptyStringType $hash);
    
    
    /**
     * Returns a hash of the given password.
     *
     * @param StringType $password
     *
     * @return string Password Hash.
     */
    public function getHash(StringType $password);
    
    
    /**
     * Returns a new hash of the given password.
     *
     * It will only differ from the given hash if the given hash is valid
     * but does match to the current auth strategy.
     *
     * @param StringType         $password
     * @param NonEmptyStringType $hash
     *
     * @return string Rehashed password.
     */
    public function getRehashedPassword(StringType $password, NonEmptyStringType $hash);
}