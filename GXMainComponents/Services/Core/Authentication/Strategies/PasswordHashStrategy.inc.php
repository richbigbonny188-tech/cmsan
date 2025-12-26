<?php
/* --------------------------------------------------------------
   PasswordHashStrategy.inc.php 2016-08-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PasswordHashStrategy
 *
 * @category   System
 * @package    Authentication
 * @subpackage Strategies
 */
class PasswordHashStrategy implements AuthStrategyInterface
{
    /**
     * Verifies a given password by its stored hash from the current hashing algorithm.
     *
     * @param StringType             $password                       Password that should be verified.
     * @param NonEmptyStringType     $hash                           Stored Hash of a password.
     * @param AuthStrategyCollection $alternativeAlgorithmCollection Collection of alternative hashing algorithms.
     *
     * @return bool Returns true if $password matches $hash, false otherwise.
     */
    public function verify(
        StringType $password,
        NonEmptyStringType $hash,
        AuthStrategyCollection $alternativeAlgorithmCollection = null
    ) {
        if (password_verify($password->asString(), $hash->asString())) {
            return true;
        }
        
        if ($alternativeAlgorithmCollection !== null) {
            /** @var AuthStrategyInterface $algorithm */
            foreach ($alternativeAlgorithmCollection->getArray() as $algorithm) {
                if ($algorithm->verify($password, $hash)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * Generates a hash by given password string.
     *
     * @param StringType $password String that should be hashed.
     *
     * @return string Resulting hash.
     * @throws RuntimeException if password_hash() could not create a hash.
     *
     */
    public function getHash(StringType $password)
    {
        $hash = password_hash($password->asString(), PASSWORD_DEFAULT);
        
        if ($hash === false) {
            throw new RuntimeException('password_hash() could not create a hash.');
        }
        
        return $hash;
    }
    
    
    /**
     * Returns a rehashed password hash if it does not match the currently used hashing algorithm.
     *
     * @param StringType                  $password                       Password that should be rehashed by a new
     *                                                                    algorithm.
     * @param NonEmptyStringType          $hash                           Current password hash.
     * @param AuthStrategyCollection|null $alternativeAlgorithmCollection Collection of alternative hashing algorithms.
     *
     * @return string The new password hash.
     */
    public function getRehashedPassword(
        StringType $password,
        NonEmptyStringType $hash,
        AuthStrategyCollection $alternativeAlgorithmCollection = null
    ) {
        if ($this->verify($password, $hash) && password_needs_rehash($hash->asString(), PASSWORD_DEFAULT)) {
            return $this->getHash($password);
        }
        
        if ($alternativeAlgorithmCollection !== null) {
            /** @var AuthStrategyInterface $algorithm */
            foreach ($alternativeAlgorithmCollection->getArray() as $algorithm) {
                if ($algorithm->verify($password, $hash)) {
                    return $this->getHash($password);
                }
            }
        }
        
        return $hash->asString();
    }
}