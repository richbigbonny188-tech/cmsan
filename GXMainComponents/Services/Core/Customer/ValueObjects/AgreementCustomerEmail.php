<?php
/* --------------------------------------------------------------
   AgreementCustomerEmail.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerEmailInterface');

/**
 * Value Object
 *
 * Class AgreementCustomerEmail
 *
 * Represents an agreement customer email
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerEmailInterface
 */
class AgreementCustomerEmail implements CustomerEmailInterface
{
    /**
     * Customers E-Mail address.
     *
     * @var string
     */
    protected $email;
    
    
    /**
     * AgreementCustomerEmail constructor.
     *
     * @param string $email                   Customer email.
     * @param bool   $encodeSpecialCharacters Encode special characters?
     */
    public function __construct($email, $encodeSpecialCharacters = true)
    {
        if (!is_string($email)) {
            throw new InvalidArgumentException('$email is not a string');
        }
        
        if (!empty($email) && $encodeSpecialCharacters) {
            $punycode = new TrueBV\Punycode();
            $email    = $punycode->encode($email);
        }
        
        if (!empty(trim($email)) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UnexpectedValueException('$email is not a valid e-mail address: ' . $email);
        }
        
        $this->email = trim($email);
    }
    
    
    /**
     * Returns the equivalent string value.
     *
     * @return string Equivalent string value.
     */
    public function __toString()
    {
        return $this->email;
    }
}