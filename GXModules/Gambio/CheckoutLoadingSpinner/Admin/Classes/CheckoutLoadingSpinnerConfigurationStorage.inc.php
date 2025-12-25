<?php
/* --------------------------------------------------------------
   CheckoutLoadingSpinnerConfigurationStorage.inc.php 2018-04-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the checkout loading spinner configuration storage
 */
class CheckoutLoadingSpinnerConfigurationStorage extends ConfigurationStorage
{
    /**
     * Configuration storage namespace
     */
    const STORAGE_NAMESPACE = 'modules/gambio/checkout_loading_spinner';
    
    /**
     * Default configuration storage
     */
    protected $defaults;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::STORAGE_NAMESPACE);
        
        $this->defaults = [
            'is_enabled' => '0',
            'timeout'    => 0
        ];
    }
    
    
    /**
     * Return a configuration value
     *
     * @param string $key Configuration key
     *
     * @return string Configuration value
     * @throws InvalidArgumentException Invalid key
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->defaults)) {
            throw new InvalidArgumentException('Invalid key');
        }
        
        $value = parent::get($key);
        
        if ($value === false && array_key_exists($key, $this->defaults)) {
            $value = $this->defaults[$key];
        }
        
        return $value;
    }
    
    
    /**
     * Return all configuration key/value pairs
     * @return array Key/Value pairs
     */
    public function getAll()
    {
        $values = parent::get_all();
        
        foreach ($this->defaults as $key => $defaultValue) {
            if (!array_key_exists($key, $values)) {
                $values[$key] = $defaultValue;
            }
        }
        
        return $values;
    }
    
    
    /**
     * Set a configuration value
     *
     * @param string $key   Configuration key
     * @param string $value Configuration value
     *
     * @return $this Same instance
     * @throws InvalidArgumentException Invalid key
     */
    public function set($key, $value)
    {
        if (!array_key_exists($key, $this->defaults)) {
            throw new InvalidArgumentException('Invalid key');
        }
        
        parent::set($key, $value);
        
        return $this;
    }
}
