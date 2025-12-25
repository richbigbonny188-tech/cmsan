<?php
/* --------------------------------------------------------------
   AuthService.inc.php 2016-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AuthService
 *
 * @category   System
 * @package    Authentication
 */
class AuthService implements AuthServiceInterface
{
    /**
     * @var AuthSettingsInterface $settings
     */
    protected $settings;
    
    /**
     * @var CustomerReadService $customerReadService
     */
    protected $customerReadService;
    
    
    /**
     * Authentication constructor.
     *
     * @param AuthSettingsInterface $settings
     * @param CustomerReadService   $customerReadService
     */
    public function __construct(AuthSettingsInterface $settings, CustomerReadService $customerReadService)
    {
        $this->settings            = $settings;
        $this->customerReadService = $customerReadService;
    }
    
    
    /**
     * Authenticates a user by its email and password stored in the given AuthCredentialsInterface object.
     *
     * @param AuthCredentialsInterface $authCredentials .
     *
     * @return bool Returns the result of the authentication.
     */
    public function authUser(AuthCredentialsInterface $authCredentials)
    {
        try {
            $customerEmail = MainFactory::create('CustomerEmail', $authCredentials->getUsername());
        } catch (UnexpectedValueException $e) {
            // e-mail address is not valid
            return false;
        }
        
        $customer = $this->customerReadService->findCustomerByEmail($customerEmail);
        
        if ($customer !== null) {
            $hash = (string)$customer->getPassword();
            
            return $this->verify(new StringType($authCredentials->getPassword()), new NonEmptyStringType($hash));
        }
        
        return false;
    }
    
    
    /**
     * Verifies a password by its hash.
     *
     * @param StringType         $password Password.
     * @param NonEmptyStringType $hash     Hash.
     *
     * @return bool Returns the result of the verification.
     */
    public function verify(StringType $password, NonEmptyStringType $hash)
    {
        return $this->settings->getAuthStrategy()->verify($password, $hash, $this->settings->getAdditionalStrategies());
    }
    
    
    /**
     * Returns a hash of the given password.
     *
     * @param StringType $password
     *
     * @return string Password Hash.
     */
    public function getHash(StringType $password)
    {
        return $this->settings->getAuthStrategy()->getHash($password);
    }
    
    
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
    public function getRehashedPassword(StringType $password, NonEmptyStringType $hash)
    {
        return $this->settings->getAuthStrategy()->getRehashedPassword($password,
                                                                       $hash,
                                                                       $this->settings->getAdditionalStrategies());
    }
}