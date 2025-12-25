<?php
/* --------------------------------------------------------------
   OrderConfigurationProvider.inc.php 2018-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderConfigurationProvider
 */
class OrderConfigurationProvider
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $table = 'gx_configurations';
    
    /**
     * @var string
     */
    protected $keyField = 'key';
    
    /**
     * @var string
     */
    protected $valueField = 'value';
    
    
    /**
     * OrderConfigurationProvider constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns the default order status id.
     *
     * @return int
     */
    public function defaultOrderStatusId()
    {
        return $this->_getConfigurationValue('DEFAULT_ORDERS_STATUS_ID');
    }
    
    
    /**
     * Returns the default customer status id.
     *
     * @return int
     */
    public function defaultCustomerStatusId()
    {
        return $this->_getConfigurationValue('DEFAULT_CUSTOMERS_STATUS_ID');
    }
    
    
    /**
     * Returns the default guest status id.
     *
     * @return int
     */
    public function defaultGuestStatusId()
    {
        
        return $this->_getConfigurationValue('DEFAULT_CUSTOMERS_STATUS_ID_GUEST');
    }
    
    
    /**
     * Returns the value of the given configuration key.
     * The result will be cast to an integer.
     *
     * @param string $configurationKey Key of expected configuration value.
     *
     * @return int
     */
    protected function _getConfigurationValue($configurationKey)
    {
        $result = $this->db->select($this->valueField)
            ->from($this->table)
            ->where($this->keyField, "configuration/$configurationKey")
            ->get()
            ->row_array();
        
        return array_key_exists($this->valueField, $result) ? (int)$result[$this->valueField] : 0;
    }
}