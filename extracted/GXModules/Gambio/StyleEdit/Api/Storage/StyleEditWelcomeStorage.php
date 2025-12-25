<?php

/* --------------------------------------------------------------
   StyleEditWelcomeStorage.inc.php 2019-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Api\Storage;

class StyleEditWelcomeStorage extends \ConfigurationStorage
{
    /**
     * Configuration storage namespace
     */
    const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/styleedit';
    
    /**
     * Secret configuration key name template
     */
    const SECRET_CONFIG_KEY_TEMPLATE = 'welcomed/{CUSTOMER_ID}';
    
    
    /**
     * StyleEditWelcomeStorage constructor.
     */
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return \Gambio\StyleEdit\Api\Storage\StyleEditWelcomeStorage
     */
    public function storeWelcomeStatusSeenForCustomer(\IdType $customerId)
    {
        $key = $this->configurationKeyForCustomer($customerId);
        
        $this->set($key, '1');
        
        return $this;
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return bool has the logged in user been welcomed? (StarUp-view of theme list)
     */
    public function welcomeStatusSeenForCustomer(\IdType $customerId)
    {
        $key   = $this->configurationKeyForCustomer($customerId);
        $value = $this->get($key);
        
        return $value ? : false;
    }
    
    
    /**
     * Return the substituted configuration key string for the provided customer ID
     *
     * @param \IdType $customerId Customer ID
     *
     * @return string Substituted secret configuration key
     */
    protected function configurationKeyForCustomer(\IdType $customerId)
    {
        $id = (string)$customerId->asInt();
        
        return str_replace('{CUSTOMER_ID}', $id, self::SECRET_CONFIG_KEY_TEMPLATE);
    }
}