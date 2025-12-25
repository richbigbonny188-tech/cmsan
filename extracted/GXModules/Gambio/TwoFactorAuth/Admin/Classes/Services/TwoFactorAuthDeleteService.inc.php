<?php

/* --------------------------------------------------------------
   TwoFactorAuthDeleteService.inc.php 2018-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization delete service
 */
class TwoFactorAuthDeleteService
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
     * Remove secret and safety file for the provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return $this Same instance
     */
    public function secretAndSafetyFileForCustomer(IdType $customerId)
    {
        $this->configurationStorage->removeSecretForCustomer($customerId);
        $this->safetyFileStorage->removeFileForCustomer($customerId);
        
        return $this;
    }
}