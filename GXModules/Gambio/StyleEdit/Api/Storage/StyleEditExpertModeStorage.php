<?php
/* --------------------------------------------------------------
   StyleEditExpertModeStorage.inc.php 2019-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
   todo : Create new ConfigurationStorage-class that is testable and uses query builder
*/

namespace Gambio\StyleEdit\Api\Storage;

use ConfigurationStorage;

/**
 * Class StyleEditExpertModeStorage
 * @package Gambio\StyleEdit\Api\Storage
 */
class StyleEditExpertModeStorage extends ConfigurationStorage
{
    /**
     * Configuration storage namespace
     *
     * @var string
     */
    protected const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/styleedit';
    
    /**
     * Secret configuration key name template
     *
     * @var string
     */
    protected const SECRET_CONFIG_KEY_TEMPLATE = 'expertMode/{CUSTOMER_ID}';
    
    
    /**
     * StyleEditExpertModeStorage constructor.
     */
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
    }
    
    
    /**
     * @param \IdType $customerId
     *
     * @return bool
     */
    public function expertModeActive(\IdType $customerId): bool
    {
        $key   = $this->configurationKeyForCustomer($customerId);
        $value = $this->get($key);
        
        return (int)$value === 1;
    }
    
    
    /**
     * @param \IdType $customerId
     * @param bool    $status
     */
    public function setExpertMode(\IdType $customerId, bool $status): void
    {
        $key = $this->configurationKeyForCustomer($customerId);
        
        $this->set($key, $status ? 1 : 0);
    }
    
    
    /**
     * Return the substituted configuration key string for the provided customer ID
     *
     * @param \IdType $customerId Customer ID
     *
     * @return string Substituted secret configuration key
     */
    protected function configurationKeyForCustomer(\IdType $customerId): string
    {
        $id = (string)$customerId->asInt();
        
        return str_replace('{CUSTOMER_ID}', $id, self::SECRET_CONFIG_KEY_TEMPLATE);
    }
}