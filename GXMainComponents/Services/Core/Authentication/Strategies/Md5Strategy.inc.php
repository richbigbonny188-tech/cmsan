<?php
/* --------------------------------------------------------------
   Md5Strategy.inc.php 2016-08-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Md5Strategy
 *
 * @category   System
 * @package    Authentication
 * @subpackage Strategies
 */
class Md5Strategy implements AuthStrategyInterface
{
    /**
     * Verifies a given password by its stored hash from the current hashing algorithm.
     *
     * @param StringType             $password
     * @param NonEmptyStringType     $hash
     * @param AuthStrategyCollection $authStrategyCollection
     *
     * @return bool Is the password valid?
     */
    public function verify(
        StringType $password,
        NonEmptyStringType $hash,
        AuthStrategyCollection $authStrategyCollection = null
    ) {
        if (md5($password->asString()) === $hash->asString()) {
            return true;
        }
        
        if ($authStrategyCollection !== null) {
            /** @var AuthStrategyInterface $algorithm */
            foreach ($authStrategyCollection->getArray() as $algorithm) {
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
        return md5($password->asString());
    }
    
    
    /**
     * Returns a rehashed password hash if it does not match the currently used hashing algorithm.
     *
     * @param StringType                  $password                       Password that should be rehashed by a new
     *                                                                    algorithm.
     * @param NonEmptyStringType          $hash                           Current password hash.
     * @param AuthStrategyCollection|null $authStrategyCollection         Collection of hashing algorithms.
     *
     * @return string The new password hash.
     */
    public function getRehashedPassword(
        StringType $password,
        NonEmptyStringType $hash,
        AuthStrategyCollection $authStrategyCollection = null
    ) {
        if ($authStrategyCollection !== null) {
            /** @var AuthStrategyInterface $algorithm */
            foreach ($authStrategyCollection->getArray() as $algorithm) {
                if ($algorithm->verify($password, $hash)) {
                    return $this->getHash($password);
                }
            }
        }
        
        return $hash->asString();
    }
}