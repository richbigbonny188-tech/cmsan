<?php
/* --------------------------------------------------------------
   CustomerGuestPassword.inc.php 2019-03-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerPasswordInterface');

/**
 * Class CustomerGuestPassword
 *
 * Instances of this class represent guests’ pseudo-passwords. The password itself ought to be a high-entropy
 * pseudo-random string.
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerPasswordInterface
 */
class CustomerGuestPassword implements CustomerPasswordInterface
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
     * @param string|StringType $p_password Customer's password.
     *
     * @throws InvalidArgumentException If $p_password is not a string.
     */
    public function __construct($p_password)
    {
        if (!(is_string($p_password) || $p_password instanceof StringType)) {
            throw new InvalidArgumentException('$p_password is not a string');
        }
        
        $this->password = is_string($p_password) ? $p_password : $p_password->asString();
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
}
