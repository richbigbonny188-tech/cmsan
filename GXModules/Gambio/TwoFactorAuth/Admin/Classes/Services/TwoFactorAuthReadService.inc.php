<?php

/* --------------------------------------------------------------
   TwoFactorAuthReadService.inc.php 2022-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization read service
 */
class TwoFactorAuthReadService
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
     * Tolerated time window drift
     * @var IntType
     */
    protected $toleratedDelay;
    
    
    /**
     * Create instance
     *
     * @param AuthConfigurationStorage $configurationStorage Configuration storage
     * @param AuthSafetyFileStorage    $safetyFileStorage    Safety file storage
     * @param IntType                  $toleratedDelay       Tolerated time window drift
     */
    public function __construct(
        AuthConfigurationStorage $configurationStorage,
        AuthSafetyFileStorage $safetyFileStorage,
        IntType $toleratedDelay
    ) {
        $this->configurationStorage = $configurationStorage;
        $this->safetyFileStorage    = $safetyFileStorage;
        $this->toleratedDelay       = $toleratedDelay;
    }
    
    
    /**
     * Return whether the provided customer has a secret and a safety file
     * This method should be used to check whether the provided customer is using 2FA
     *
     * @param IdType $customerId Customer ID
     *
     * @return bool Whether the provided customer uses 2FA
     */
    public function usageStatusForCustomer(IdType $customerId)
    {
        try {
            $hasSecret     = (boolean)$this->configurationStorage->secretForCustomer($customerId)->code();
            $hasSafetyFile = $this->safetyFileStorage->fileExistenceForCustomer($customerId);
            
            return ($hasSecret && $hasSafetyFile);
        } catch (AuthConfigurationStorageEntryNotFoundException $exception) {
            return false;
        }
    }
    
    
    /**
     * Return whether the provided token is valid for the provided customer ID
     *
     * @param AuthToken $token      Token
     * @param IdType    $customerId Customer ID
     *
     * @return bool Whether the provided token is valid for the provided customer
     */
    public function tokenValidityForCustomer(AuthToken $token, IdType $customerId)
    {
        try {
            $secret = $this->configurationStorage->secretForCustomer($customerId);
            
            return (new TwoFactorAuthAuthenticator())->verifyCode($secret->code(),
                                                                  $token->code(),
                                                                  $this->toleratedDelay->asInt());
        } catch (AuthConfigurationStorageEntryNotFoundException $exception) {
            return false;
        }
    }
    
    
    /**
     * Return whether the provided token is valid for the provided secret
     *
     * @param AuthToken  $token  Token
     * @param AuthSecret $secret Secret
     *
     * @return bool Whether the provided is valid for the provided secret
     */
    public function tokenValidityForSecret(AuthToken $token, AuthSecret $secret)
    {
        try {
            return (new TwoFactorAuthAuthenticator())->verifyCode($secret->code(),
                                                                  $token->code(),
                                                                  $this->toleratedDelay->asInt());
        } catch (AuthConfigurationStorageEntryNotFoundException $exception) {
            return false;
        }
    }
    
    
    /**
     * Return the token for the provided customer ID
     *
     * @param IdType       $customerId Customer ID
     * @param IntType|null $timestamp  Timestamp
     *
     * @return AuthToken Token for the provided customer ID
     */
    public function tokenForCustomer(IdType $customerId, IntType $timestamp = null)
    {
        $secret = $this->configurationStorage->secretForCustomer($customerId);
        
        if ($timestamp === null) {
            $timestamp = TwoFactorAuthAuthenticator::currentTimestamp();
        }
        
        $token = (new TwoFactorAuthAuthenticator())->getCode($secret->code(), $timestamp);
        
        return AuthToken::withCode(new NonEmptyStringType($token));
    }
    
    
    /**
     * Return the token for the provided secret
     *
     * @param AuthSecret   $secret    Secret
     * @param IntType|null $timestamp Timestamp
     *
     * @return AuthToken Token for the provided secret
     */
    public function tokenForSecret(AuthSecret $secret, IntType $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = TwoFactorAuthAuthenticator::currentTimestamp();
        }
        
        $token = (new TwoFactorAuthAuthenticator())->getCode($secret->code(), $timestamp);
        
        return AuthToken::withCode(new NonEmptyStringType($token));
    }
    
    
    /**
     * Return a random secret
     * @return AuthSecret Randomly created secret
     */
    public function randomSecret()
    {
        return AuthSecret::byRandomValue();
    }
    
    
    /**
     * Return whether a safety file exists for the provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return bool Whether a safety file exists for the provided customer ID
     */
    public function safetyFileExistenceForCustomer(IdType $customerId)
    {
        return $this->safetyFileStorage->fileExistenceForCustomer($customerId);
    }
}