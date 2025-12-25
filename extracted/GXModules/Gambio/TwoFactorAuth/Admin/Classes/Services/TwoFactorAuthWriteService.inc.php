<?php

/* --------------------------------------------------------------
   TwoFactorAuthWriteService.inc.php 2018-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization write service
 */
class TwoFactorAuthWriteService
{
    /**
     * Configuration storage
     * @var AuthConfigurationStorage
     */
    protected $configurationStorage;
    
    /**
     * Safety file storage
     * @var AuthSafetyFileStorage
     */
    protected $safetyFileStorage;
    
    
    /**
     * Create instance
     *
     * @param AuthConfigurationStorage $configurationStorage Configuration storage
     * @param AuthSafetyFileStorage    $safetyFileStorage    Safety file storage
     */
    public function __construct(
        AuthConfigurationStorage $configurationStorage,
        AuthSafetyFileStorage $safetyFileStorage
    ) {
        $this->configurationStorage = $configurationStorage;
        $this->safetyFileStorage    = $safetyFileStorage;
    }
    
    
    /**
     * Store a secret for the provided customer ID
     *
     * @param IdType          $customerId     Customer ID
     * @param AuthSecret|null $providedSecret If omitted, a random secret is used
     *
     * @return $this Same instance
     */
    public function secretForCustomer(IdType $customerId, AuthSecret $providedSecret = null)
    {
        try {
            $secret = $this->configurationStorage->secretForCustomer($customerId);
            
            if ($secret->code()) {
                $this->configurationStorage->removeSecretForCustomer($customerId);
            }
        } catch (Exception $exception) {
        }
        
        $secret = $providedSecret ? : AuthSecret::byRandomValue();
        
        $this->configurationStorage->storeSecretForCustomer($customerId, $secret);
        
        return $this;
    }
    
    
    /**
     * Create a safety file for the provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return $this Same instance
     */
    public function safetyFileForCustomer(IdType $customerId)
    {
        $hasAlreadyFile = $this->safetyFileStorage->fileExistenceForCustomer($customerId);
        
        if (!$hasAlreadyFile) {
            $this->safetyFileStorage->createFileForCustomer($customerId);
        }
        
        return $this;
    }
}