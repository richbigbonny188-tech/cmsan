<?php
/* --------------------------------------------------------------
   CustomerPassword.inc.php 2016-09-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerPasswordInterface');

/**
 * Class CustomerPassword
 *
 * Represents a customer's password.
 *
 * @deprecated This class is deprecated and will be deleted in GX 3.7. Use CustomerHashedPassword instead.
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerPasswordInterface
 */
class CustomerPassword implements CustomerPasswordInterface
{
    /**
     * Customer's password.
     *
     * @var string
     */
    protected $password;
    
    
    /**
     * Constructor for the class CustomerPassword.
     *
     * Validates password and build md5-hash.
     *
     * @param string $p_password    Customer's password.
     * @param bool   $p_disableHash (optional) Will not hash the provided password string.
     *
     * @throws InvalidArgumentException If $p_password is not a string.
     */
    public function __construct($p_password, $p_disableHash = false)
    {
        if (!is_string($p_password)) {
            throw new InvalidArgumentException('$p_password is not a string');
        }
        
        if (!is_bool($p_disableHash)) {
            throw new InvalidArgumentException('$p_disableHash is not a bool');
        }
        
        $this->password = $p_disableHash === false ? $this->_getHash($p_password) : $p_password;
    }
    
    
    /**
     * Returns the password string value (either hashed or not).
     *
     * @return string
     */
    public function __toString()
    {
        return $this->password;
    }
    
    
    /**
     * Get password hash string.
     *
     * @param string $p_password Password to be hashed.
     *
     * @return string
     *
     * @throws RuntimeException If the password_hash function fails to hash the password.
     * @deprecated Automatic password hashing is deprecated. Use the AuthService::getHash() for hashing instead.
     *
     */
    protected function _getHash($p_password)
    {
        return md5($p_password);
    }
} 