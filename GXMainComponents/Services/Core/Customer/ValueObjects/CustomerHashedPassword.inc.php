<?php
/* --------------------------------------------------------------
   CustomerHashedPassword.inc.php 2016-09-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerPasswordInterface');

/**
 * Class CustomerHashedPassword
 *
 * Represents a customer's password hash.
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerPasswordInterface
 */
class CustomerHashedPassword implements CustomerPasswordInterface
{
    /**
     * Customer's password hash.
     *
     * @var string
     */
    protected $password;
    
    
    /**
     * Constructor for the class CustomerHashedPassword.
     *
     * @param NonEmptyStringType $password Password hash.
     */
    public function __construct(NonEmptyStringType $password)
    {
        $this->password = $password->asString();
    }
    
    
    /**
     * Returns the password hash as string value.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->password;
    }
}