<?php
/* --------------------------------------------------------------
   UsernamePasswordCredentials.inc.php 2016-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UsernamePasswordCredentials
 *
 * @category   System
 * @package    Authentication
 * @subpackage Entities
 */
class UsernamePasswordCredentials implements AuthCredentialsInterface
{
    /**
     * @var string $username
     */
    protected $username;
    
    /**
     * @var string $password
     */
    protected $password;
    
    
    /**
     * UsernamePasswordCredentials constructor.
     *
     * @param NonEmptyStringType $username
     * @param StringType         $password
     */
    public function __construct(NonEmptyStringType $username, StringType $password)
    {
        $this->username = $username->asString();
        $this->password = $password->asString();
    }
    
    
    /**
     * Returns the username.
     *
     * @return string Username.
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    
    /**
     * Returns the password.
     *
     * @return string Password.
     */
    public function getPassword()
    {
        return $this->password;
    }
}