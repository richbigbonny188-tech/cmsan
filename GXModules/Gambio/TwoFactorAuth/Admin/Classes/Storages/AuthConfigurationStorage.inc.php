<?php

/* --------------------------------------------------------------
   AuthConfigurationStorage.inc.php 2018-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a configuration storage
 */
class AuthConfigurationStorage extends ConfigurationStorage
{
    /**
     * Configuration storage namespace
     */
    const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/2fa';
    
    /**
     * Secret configuration key name template
     */
    const SECRET_CONFIG_KEY_TEMPLATE = 'secrets/{CUSTOMER_ID}';
    
    
    /**
     * Create instance
     */
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
    }
    
    
    /**
     * Store the secret for the provided customer ID
     *
     * @param IdType     $customerId Customer ID
     * @param AuthSecret $secret     Secret
     *
     * @return $this Same instance
     */
    public function storeSecretForCustomer(IdType $customerId, AuthSecret $secret)
    {
        $key   = $this->configurationKeyForCustomer($customerId);
        $value = $secret->code();
        
        parent::set($key, $value);
        
        return $this;
    }
    
    
    /**
     * Return the stored secret for the provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return AuthSecret Secret
     * @throws AuthConfigurationStorageEntryNotFoundException When an entry is not found
     */
    public function secretForCustomer(IdType $customerId)
    {
        $key   = $this->configurationKeyForCustomer($customerId);
        $value = parent::get($key);
        
        if (!$value) {
            throw new AuthConfigurationStorageEntryNotFoundException('Could not find entry for customer ID '
                                                                     . $customerId->asInt());
        }
        
        $typed = new NonEmptyStringType($value);
        
        return AuthSecret::withCode($typed);
    }
    
    
    /**
     * Remove the stored secret for the provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return $this Same instance
     */
    public function removeSecretForCustomer(IdType $customerId)
    {
        $key = $this->configurationKeyForCustomer($customerId);
        
        parent::delete($key);
        
        return $this;
    }
    
    
    /**
     * Return the substituted secret configuration key string for the provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return string Substituted secret configuration key
     */
    protected function configurationKeyForCustomer(IdType $customerId)
    {
        $id = (string)$customerId->asInt();
        
        return str_replace('{CUSTOMER_ID}', $id, self::SECRET_CONFIG_KEY_TEMPLATE);
    }
}