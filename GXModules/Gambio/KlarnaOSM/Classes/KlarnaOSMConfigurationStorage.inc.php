<?php
/* --------------------------------------------------------------
   KlarnaOSMConfigurationStorage.inc.php 2022-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class KlarnaOSMConfigurationStorage extends \ConfigurationStorage
{
    /**
     * namespace inside the configuration storage
     */
    const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/klarnaosm';
    
    /**
     * array holding default values to be used in absence of configured values
     */
    protected $default_configuration;
    
    
    /**
     * constructor; initializes default configuration
     */
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
        $this->setDefaultConfiguration();
    }
    
    
    /**
     * fills $default_configuration with initial values
     */
    protected function setDefaultConfiguration()
    {
        $this->default_configuration = [
            'active'                    => '0',
            'cookie_consent_purpose_id' => 0,
            'snippet_library'           => '',
            'snippet_product_top'       => '',
            'snippet_product_bottom'    => '',
            'snippet_cart'              => '',
            'snippet_cart_top'          => '',
            'snippet_footer_bottom'     => '',
        ];
    }
    
    
    /**
     * returns a single configuration value by its key
     *
     * @param string $key a configuration key (relative to the namespace prefix)
     *
     * @return string configuration value
     */
    public function get($key)
    {
        $value = parent::get($key);
        if ($value === false && array_key_exists($key, $this->default_configuration)) {
            $value = $this->default_configuration[$key];
        }
        
        return $value;
    }
    
    
    /**
     * Retrieves all keys/values from a given prefix namespace
     *
     * @param string $p_prefix
     *
     * @return array
     */
    public function get_all($p_prefix = '')
    {
        $values = parent::get_all($p_prefix);
        foreach ($this->default_configuration as $key => $default_value) {
            $key_prefix = substr($key, 0, strlen($p_prefix));
            if (!array_key_exists($key, $values) && $key_prefix === $p_prefix) {
                $values[$key] = $default_value;
            }
        }
        
        return $values;
    }
    
    
    public function set($p_key, $p_value)
    {
        switch ($p_key) {
            case 'snippet_library':
            case 'snippet_product_top':
            case 'snippet_product_bottom':
            case 'snippet_cart':
            case 'snippet_cart_top':
            case 'snippet_footer_bottom':
                $value = trim((string)$p_value);
                break;
            case 'active';
                $value = (bool)$p_value ? '1' : '0';
                break;
            case 'cookie_consent_purpose_id':
                $value = (int)$p_value;
                break;
            default:
                throw new \RuntimeException('invalid configuration key');
        }
        
        parent::set($p_key, $value);
    }
    
}