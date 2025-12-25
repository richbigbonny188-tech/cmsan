<?php
/* --------------------------------------------------------------
	GXModuleConfigurationStorage.inc.php 2021-07-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2021 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * subclass of ConfigurationStorage for parameters concerning GXModules
 */
class GXModuleConfigurationStorage extends ConfigurationStorage
{
    /**
     * Holds the configuration from GXModule.json file
     *
     * @var array|bool
     */
    protected $config;
    
    
    /**
     * constructor; initializes namespace for GXModule configuration
     * with correct namespace
     *
     * @param string $module VendorModulename directory of GXModule
     */
    public function __construct($module)
    {
        $this->config = $this->_getGXModuleJSONConfiguration($module);
        $namespace    = 'modules/' . str_replace('/', '', $module);
        parent::__construct($namespace);
    }
    
    
    /**
     * Returns a single configuration value by its key
     * if stored as json decode automaticaly
     *
     * @param string       $key          a configuration key (relative to the namespace prefix)
     * @param LanguageCode $languageCode Optional for language dependent config values.
     *
     * @return string|array|null configuration value
     */
    public function get($key, LanguageCode $languageCode = null)
    {
        $value = parent::get($key);
        
        if ($this->isJson($value)) {
            $value = json_decode($value, false, 512, JSON_BIGINT_AS_STRING);
        }
        
        if ($value === false) {
            $value = $this->_getDefaultValueFromJsonFile($key);
        }
        if ($languageCode !== null) {
            $languageCode = strtolower($languageCode->asString());
            if (is_array($value)) {
                $value = isset($value[$languageCode]) ? $value[$languageCode] : null;
            } elseif (is_object($value)) {
                $value = isset($value->$languageCode) ? $value->$languageCode : null;
            }
        }
        
        return $value;
    }
    
    
    /**
     * Helper function to check if string is json formated
     *
     * @param string $string
     *
     * @return bool
     */
    protected function isJson($string)
    {
        json_decode($string);
        
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    
    /**
     * Get the json configuration from GXModule.json file
     *
     * @param $name
     *
     * @return array|bool
     */
    protected function _getGXModuleJSONConfiguration($name)
    {
        $gxModuleFiles = GXModulesCache::getFiles();
        
        foreach ($gxModuleFiles as $file) {
            if (strpos($file, 'GXModule.json') !== false) {
                preg_match("/GXModules\/(.*)\/GXModule.json/", $file, $matches);
                $moduleData = json_decode(file_get_contents($file), true);
                if (str_replace('/', '', $matches[1]) === str_replace('/', '', $name)) {
                    return $moduleData;
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * Returns the default value of the configuration key
     *
     * @param $key
     *
     * @return mixed
     */
    protected function _getDefaultValueFromJsonFile($key)
    {
        if (!isset($this->config['configuration'])) {
            return false;
        }
        
        foreach ($this->config['configuration'] as $section => $fields) {
            foreach ($fields as $field) {
                if (isset($field[$key]['default_value']) && $field[$key]['type'] !== 'multiselect') {
                    return $field[$key]['default_value'];
                } elseif (isset($field[$key]['selected']) && $field[$key]['type'] === 'multiselect') {
                    return $field[$key]['selected'];
                }
            }
        }
        
        return false;
    }
    
}
