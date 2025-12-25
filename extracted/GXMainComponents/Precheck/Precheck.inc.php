<?php
/* --------------------------------------------------------------
   Precheck.php 2023-04-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Precheck
 *
 * @category System
 * @package  Precheck
 */
class Precheck
{
    /**
     * Minimum PHP version required.
     * @var string
     */
    protected $minPhpVersion = '';
    
    /**
     * Maximum PHP version required.
     * @var string
     */
    protected $maxPhpVersion = '';
    
    /**
     * Minimum MySQL version required.
     * @var string
     */
    protected $minMysqlVersion = '';
    
    /**
     * Minimum MySQL version required.
     * @var string
     */
    protected $maxMysqlVersion = '';
    
    /**
     * Collection of globs to find dangerous tools
     * like 'adminer' or 'dumper'.
     * @var array
     */
    protected $dangerousToolsGlobs;
    
    /**
     * Database query builder.
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * Collection of variables used.
     * @var KeyValueCollection
     */
    protected $settings;
    
    /**
     * Found configuration values.
     * @var KeyValueCollection
     */
    protected $configuration;
    
    /**
     * Found shop version.
     * @var string
     */
    protected $shopVersion = '';
    
    /**
     * Array of known languages (mostly the default installed ones).
     * @var array
     */
    protected $defaultLanguages;
    
    /**
     * Glob search pattern for usermods.
     * @var string
     */
    protected $usermodGlobPattern;
    
    
    /**
     * Precheck constructor.
     *
     * @param CI_DB_query_builder $db       Database query builder.
     * @param KeyValueCollection  $settings Settings key value collection (see #_checkSettings).
     */
    public function __construct(CI_DB_query_builder $db, KeyValueCollection $settings)
    {
        // Set database driver.
        $this->db = $db;
        
        // Check and set some configure.php values.
        $this->_checkSettings($settings);
        $this->settings = $settings;
        
        // Set minimum and maximum PHP versions required.
        $this->minPhpVersion = '5.4';
        $this->maxPhpVersion = '5.6.999';
        
        // Set minimum and maximum MySQL versions required.
        $this->minMysqlVersion = '5.0';
        $this->maxMysqlVersion = '5.9';
        
        // Set dangerous tools.
        $this->dangerousToolsGlobs = [
            '*MyAdmin*',
            '*NewAdmin*',
            '*phpm*',
            '*msd*',
            '*dumper*',
            'mybackup*',
            '*admin_access*',
            '*adminer*',
            '__*'
        ];
        
        // Set known languages.
        $this->defaultLanguages = ['de', 'en'];
        
        // Set configuration values.
        $this->configuration = $this->_getConfigurationValues();
        
        // Set usermod glob search pattern.
        $this->usermodGlobPattern = '*-USERMOD.*';
    }
    
    
    /**
     * Verifies that the settings collection parameter contains all necessary entries.
     *
     * Needed keys are:
     *  - shopVersion (Current shop version - coming from release info)
     *  - DIR_FS_CATALOG (Value from configure.php)
     *  - HTTP_SERVER (Value from configure.php)
     *  - DIR_WS_CATALOG (Value from configure.php)
     *
     * @param KeyValueCollection $settings
     *
     * @protected
     * @throws InvalidArgumentException On missing key.
     *
     */
    protected function _checkSettings(KeyValueCollection $settings)
    {
        $neededKeys = ['shopVersion', 'DIR_FS_CATALOG', 'HTTP_SERVER', 'DIR_WS_CATALOG'];
        
        foreach ($neededKeys as $key) {
            if (!$settings->keyExists($key)) {
                throw new InvalidArgumentException('Missing setting ' . $key);
            }
        }
    }
    
    
    /**
     * Loads the configuration from the database and returns it as collection.
     * @return KeyValueCollection
     * @protected
     */
    protected function _getConfigurationValues()
    {
        $namespace = 'configuration/';
        $configurationValues = [];
        
        $dbTableName = 'gx_configurations';
        $dbQuery     = $this->db->from($dbTableName)->like('key', $namespace, 'right')->get();
        $result      = $dbQuery->result_array();
        
        foreach ($result as $row) {
            $key   = str_replace($namespace, '', $row['key']);
            $value = $row['value'];
            
            $configurationValues[$key] = $value;
        }
        
        $collection = new KeyValueCollection($configurationValues);
        
        return $collection;
    }
    
    
    /**
     * Performs a recursive glob search.
     *
     * @param string $pattern Glob search pattern.
     * @param int    $flags   Glob search flags.
     *
     * @return array
     * @protected
     */
    protected function _recursiveGlobSearch($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        
        if (is_array($files)) {
            $dirs = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
            
            if (is_array($dirs)) {
                foreach ($dirs as $dir) {
                    $files = array_merge($files, $this->_recursiveGlobSearch($dir . '/' . basename($pattern), $flags));
                }
            }
        } else {
            $files = [];
        }
        
        return $files;
    }
    
    
    /**
     * Determines if the foreign module Orgamax is installed.
     * @return bool
     * @protected
     */
    protected function _hasForeignModuleOrgamax()
    {
        $shopRootDir = $this->settings->getValue('DIR_FS_CATALOG');
        $condition   = file_exists($shopRootDir . 'orgamax') || file_exists($shopRootDir . 'Orgamax')
                       || file_exists($shopRootDir . 'orga');
        
        return $condition;
    }
    
    
    /**
     * Determines if the foreign module PDF Katalog (Estelco) is installed.
     * @return bool
     * @protected
     */
    protected function _hasForeignModuleEstelcoPdfCatalog()
    {
        $shopRootDir = $this->settings->getValue('DIR_FS_CATALOG');
        $condition   = file_exists($shopRootDir . 'admin/pdf_config.php')
                       && file_exists($shopRootDir . 'admin/pdf_export.php');
        
        return $condition;
    }
    
    
    /**
     * Determines if the foreign module JTL Wawi Connector is installed.
     * @return bool
     * @protected
     */
    protected function _hasForeignModuleJtlWawiConnector()
    {
        $shopRootDir = $this->settings->getValue('DIR_FS_CATALOG');
        $condition   = file_exists($shopRootDir . 'admin/includes/modules/jtlwawi_connector')
                       && is_dir($shopRootDir . 'admin/includes/modules/jtlwawi_connector');
        
        return $condition;
    }
    
    
    /**
     * Determines if the foreign module Cateno Shopsync is installed.
     * @return bool
     * @protected
     */
    protected function _hasForeignModuleCatenoShopsync()
    {
        $shopRootDir = $this->settings->getValue('DIR_FS_CATALOG');
        $condition   = file_exists($shopRootDir . 'shopsync') && is_dir($shopRootDir . 'shopsync');
        
        return $condition;
    }
    
    
    /**
     * Returns the shop's URL.
     * @return string
     */
    public function getShopUrl()
    {
        $shopUrl = $this->settings->getValue('HTTP_SERVER') . $this->settings->getValue('DIR_WS_CATALOG');
        
        return $shopUrl;
    }
    
    
    /**
     * Returns the installed shop version.
     * @return string
     */
    public function getShopVersion()
    {
        $version = $this->settings->getValue('shopVersion');
        
        return $version;
    }
    
    
    /**
     * Returns the server address.
     * @return string
     */
    public function getServerAddress()
    {
        $serverAddress = $_SERVER['SERVER_ADDR'];
        
        return $serverAddress;
    }
    
    
    /**
     * Returns the server path.
     * @return string
     */
    public function getServerPath()
    {
        $serverPath = rtrim(DIR_FS_CATALOG, '/') . '/';
        
        return $serverPath;
    }
    
    
    /**
     * Returns the server's operating system.
     * @return string
     */
    public function getServerOs()
    {
        $os = PHP_OS;
        
        return $os;
    }
    
    
    /**
     * Returns the running PHP version number.
     * @return string
     */
    public function getPhpVersion()
    {
        $phpVersion = PHP_VERSION;
        
        return $phpVersion;
    }
    
    
    /**
     * Returns the running MySQL version number.
     * @return string
     */
    public function getMysqlVersion()
    {
        $mysqlVersion = $this->db->version();
        
        return $mysqlVersion;
    }
    
    
    /**
     * Determines if cUrl is available on this platform.
     * @return bool
     */
    public function isCurlEnabled()
    {
        $isCallableCurlInit = is_callable('curl_init');
        
        return $isCallableCurlInit;
    }
    
    
    /**
     * Returns the name of the currently used template.
     * 
     * @deprecated since GX 4.5
     * 
     * @return string
     */
    public function getActiveTemplate()
    {
        return '';
    }
    
    
    /**
     * Returns an array of additional installed templates (template name).
     * @return array
     */
    public function getAdditionalTemplates()
    {
        return [];
    }
    
    
    /**
     * Returns an array of usermods found.
     * @return array
     */
    public function getUsermods()
    {
        $shopRootPath  = $this->settings->getValue('DIR_FS_CATALOG');
        $foundUsermods = [];
        $searchPaths   = [
            // Admin HTML usermods search path.
            $shopRootPath . 'admin/html/',
            
            // Admin JavaScript usermods search path.
            $shopRootPath . 'admin/javascript/',
            
            // Shop JavaScript usermods search path.
            $shopRootPath . 'gm/javascript/',
        ];
        
        foreach ($searchPaths as $path) {
            if (file_exists($path)) {
                $foundFiles = $this->_recursiveGlobSearch($path . $this->usermodGlobPattern);
                
                if (count($foundFiles) > 0) {
                    foreach ($foundFiles as $file) {
                        $foundUsermods[] = substr($file, strlen($shopRootPath));
                    }
                }
            }
        }
        
        return $foundUsermods;
    }
    
    
    /**
     * Determines if the MobileCandy template is installed.
     * 
     * @deprecated since GX 4.5
     * 
     * @return bool
     */
    public function isInstalledMobileCandy()
    {
        return false;
    }
    
    
    /**
     * Returns an array of MobileCandy usermods found.
     *
     * @deprecated since GX 4.5
     * 
     * @return array
     */
    public function getMobileCandyUsermods()
    {
        return [];
    }
    
    
    /**
     * Determines if there is a global usermod directory.
     * @return bool
     */
    public function hasGlobalUsermodDir()
    {
        $directoryPath = $this->settings->getValue('DIR_FS_CATALOG') . 'USERMOD';
        $hasDir        = file_exists($directoryPath);
        
        return $hasDir;
    }
    
    
    /**
     * Returns an array of custom user components.
     * @return array
     */
    public function getUserComponents()
    {
        return [];
    }
    
    
    /**
     * Returns an array of foreign installed payment modules.
     *
     * @param array $defaultModules          List of default payment modules (note, that a file path has to be
     *                                       relative to the payment directory).
     *
     * @return array
     * @throws InvalidArgumentException On empty array.
     */
    public function getForeignPaymentModules(array $defaultModules)
    {
        $directory             = $this->settings->getValue('DIR_FS_CATALOG') . 'includes/modules/payment/';
        $globPattern           = '*.*';
        $modules               = [];
        $foundInstalledModules = $this->_recursiveGlobSearch($directory . $globPattern);
        
        foreach ($foundInstalledModules as $file) {
            $modules[] = str_replace($directory, '', $file);
        }
        
        foreach ($defaultModules as $file) {
            if (in_array(trim($file), $modules)) {
                $index = array_search(trim($file), $modules);
                unset($modules[$index]);
            }
        }
        
        return $modules;
    }
    
    
    /**
     * Returns an array of foreign installed shipping modules.
     *
     * @param array $defaultModules          List of default shipping modules (note, that a file path has to be
     *                                       relative to the shipping directory).
     *
     * @return array
     * @throws InvalidArgumentException On empty array.
     */
    public function getForeignShippingModules(array $defaultModules)
    {
        $directory             = $this->settings->getValue('DIR_FS_CATALOG') . 'includes/modules/shipping/';
        $globPattern           = '*.*';
        $modules               = [];
        $foundInstalledModules = $this->_recursiveGlobSearch($directory . $globPattern);
        
        foreach ($foundInstalledModules as $file) {
            $modules[] = str_replace($directory, '', $file);
        }
        
        foreach ($defaultModules as $file) {
            if (in_array(trim($file), $modules)) {
                $index = array_search(trim($file), $modules);
                unset($modules[$index]);
            }
        }
        
        return $modules;
    }
    
    
    /**
     * Returns an array of foreign installed order total modules.
     *
     * @param array $defaultModules          List of default order total modules (note, that a file path has to be
     *                                       relative to the order_total directory).
     *
     * @return array
     * @throws InvalidArgumentException On empty array.
     */
    public function getForeignOrderTotalModules(array $defaultModules)
    {
        $directory             = $this->settings->getValue('DIR_FS_CATALOG') . 'includes/modules/order_total/';
        $globPattern           = '*.*';
        $modules               = [];
        $foundInstalledModules = $this->_recursiveGlobSearch($directory . $globPattern);
        
        foreach ($foundInstalledModules as $file) {
            $modules[] = str_replace($directory, '', $file);
        }
        
        foreach ($defaultModules as $file) {
            if (in_array(trim($file), $modules)) {
                $index = array_search(trim($file), $modules);
                unset($modules[$index]);
            }
        }
        
        return $modules;
    }
    
    
    /**
     * Returns the installed foreign languages.
     *
     * @return array
     */
    public function getForeignModules()
    {
        $installedForeignModuleNames = [];
        
        if ($this->_hasForeignModuleOrgamax()) {
            $installedForeignModuleNames[] = 'Orgamax';
        }
        
        if ($this->_hasForeignModuleEstelcoPdfCatalog()) {
            $installedForeignModuleNames[] = 'PDF Katalog (Estelco)';
        }
        
        if ($this->_hasForeignModuleJtlWawiConnector()) {
            $installedForeignModuleNames[] = 'JTL Wawi Connector';
        }
        
        if ($this->_hasForeignModuleCatenoShopsync()) {
            $installedForeignModuleNames[] = 'Cateno Shopsync';
        }
        
        return $installedForeignModuleNames;
    }
    
    
    /**
     * Returns the installed foreign languages.
     *
     * @return array
     */
    public function getForeignLanguages()
    {
        $difference = [];
        $languages  = '"' . implode('","', $this->defaultLanguages) . '"';
        $dbQuery    = $this->db->query('SELECT code FROM languages WHERE code NOT IN (' . $languages . ')');
        
        foreach ($dbQuery->result_array() as $row) {
            $difference[] = $row['code'];
        }
        
        return $difference;
    }
    
    
    /**
     * Returns an array with dangerous tools installed.
     * @return array
     */
    public function getDangerousTools()
    {
        $foundTools   = [];
        $shopRootPath = $this->settings->getValue('DIR_FS_CATALOG');
        
        foreach ($this->dangerousToolsGlobs as $toolsGlob) {
            $files = glob($shopRootPath . $toolsGlob);
            
            if (is_array($files) && count($files) > 0) {
                foreach ($files as $file) {
                    $foundTools[] = substr($file, strlen($shopRootPath));
                }
            }
        }
        
        return $foundTools;
    }
    
    
    /**
     * Determines if the installed PHP version is above the minimum version required.
     * If true, the result is positive.
     * @return bool
     */
    public function isPhpVersionAboveMinimum()
    {
        $isAboveMinimum = version_compare($this->getPhpVersion(), $this->minPhpVersion, '>=');
        
        return $isAboveMinimum;
    }
    
    
    /**
     * Determines if the installed PHP version is below the maximum version required.
     * If true, the result is positive.
     * @return bool
     */
    public function isPhpVersionBelowMaximum()
    {
        $isBelowMaximum = version_compare($this->getPhpVersion(), $this->maxPhpVersion, '<=');
        
        return $isBelowMaximum;
    }
    
    
    /**
     * Determines if the server is running on windows operating system.
     * @return bool
     */
    public function isUsingWindowsOs()
    {
        $windowsOsIndicator  = 'WIN';
        $uppercasedOsInfo    = strtoupper($this->getServerOs());
        $cutUppercasedOsInfo = substr($uppercasedOsInfo, 0, 3);
        
        $isUsingWindows = $cutUppercasedOsInfo === $windowsOsIndicator;
        
        return $isUsingWindows;
    }
    
    
    /**
     * Determines if the installed MySQL version is above the minimum version required.
     * If true, the result is positive.
     * @return boolean
     */
    public function isMySqlVersionAboveMinimum()
    {
        $isAboveMinimum = version_compare($this->getMysqlVersion(), $this->minMysqlVersion, '>=');
        
        return $isAboveMinimum;
    }
    
    
    /**
     * Determines if the installed MySQL version is below the maximum version required.
     * If true, the result is positive.
     * @return bool
     */
    public function isMySqlVersionBelowMaximum()
    {
        $isBelowMaximum = version_compare($this->getMysqlVersion(), $this->maxMysqlVersion, '<=');
        
        return $isBelowMaximum;
    }
    
    
    /**
     * Checks if magnalister is active and the pass phrase is not empty
     *
     * @return bool
     */
    public function isMagnalisterActive(): bool
    {
        $result = $this->db->query("SHOW TABLES LIKE 'magnalister_config'");
        
        if (!$result->num_rows()) {
            return false;
        }
        
        $isMagnalisterActive = $this->db->query("SELECT * FROM `magnalister_config`
                                                     WHERE
                                                         `mkey` = 'general.passphrase' AND
                                                         `value` != ''")->num_rows();
        
        $isMagnalisterActive &= $this->db->query("SELECT * FROM `gx_configurations`
                                                    WHERE
                                                        `key` = 'configuration/MODULE_MAGNALISTER_STATUS' AND
                                                        `value` = 'True'")->num_rows();
        
        return (bool)$isMagnalisterActive;
    }
}