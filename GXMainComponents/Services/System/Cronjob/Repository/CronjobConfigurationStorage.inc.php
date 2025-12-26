<?php
/* --------------------------------------------------------------
   CronjobConfigurationStorage.inc.php 2021-09-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Subclass of ConfigurationStorage for parameters concerning cron tasks
 */
class CronjobConfigurationStorage
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \CronjobSettings
     */
    protected $cronjobSettings;
    
    
    /**
     * YetAnotherConfigurationStorage constructor.
     *
     * @param \CI_DB_query_builder $db
     * @param \CronjobSettings     $cronjobSettings
     */
    public function __construct(CI_DB_query_builder $db, CronjobSettings $cronjobSettings)
    {
        $this->db              = $db;
        $this->cronjobSettings = $cronjobSettings;
    }
    
    
    /**
     * Fetches a cronjob configuration value.
     * If nothing was found in storage, a default from the config json file will be used.s
     *
     * @param string $scope         Cronjob configuration scope, e.g.: DeleteLogs
     * @param string $configuration Configuration key, e.g.: interval
     *
     * @return mixed
     * @throws \CronjobConfigurationNotFoundException
     */
    public function get($scope, $configuration)
    {
        $key    = 'cronjobs/' . $scope . '/' . $configuration;
        $result = $this->db->select('value')->from('gx_configurations')->where('key', $key)->get()->row_array();
        
        if ($result) {
            if ($result['value'] === 'true' || $result['value'] === 'false') {
                return $result['value'] === 'true';
            }
            
            return $result['value'];
        }
        
        $configurationFile = $this->cronjobSettings->configurationDirectory() . DIRECTORY_SEPARATOR . $scope . '.json';
        if (!file_exists($configurationFile)) {
            $gxmodulesConfigurationFile = $this->findGxmodulesConfigurationFile($scope);
            if ($gxmodulesConfigurationFile !== false) {
                $configurationFile = $gxmodulesConfigurationFile;
            }
        }
        $jsonConfiguration = json_decode(file_get_contents($configurationFile), true);
        
        return $this->_getDefaultFromJson($jsonConfiguration, $configuration);
    }

    
    protected function findGxmodulesConfigurationFile($scope)
    {
        $configurationFiles = [];
        $globIterator = new \GlobIterator(DIR_FS_CATALOG . '/GXModules/*/*/Admin/CronjobConfiguration/'.$scope.'*.json');
        /** @var SplFileInfo $fileInfo */
        foreach ($globIterator as $fileInfo) {
            $configurationFiles[$scope] = $fileInfo->getPathname();
        }
    
        if (isset($configurationFiles[$scope])) {
            return $configurationFiles[$scope];
        }
        return false;
    }
    
    /**
     * Checks if a default value exist in the json configuration file for $configuration.
     * If not, an exception is thrown.
     *
     * @param array  $jsonConfiguration Json configuration data.
     * @param string $configuration     Expected configuration field.
     *
     * @return mixed
     * @throws \CronjobConfigurationNotFoundException
     */
    protected function _getDefaultFromJson(array $jsonConfiguration, $configuration)
    {
        if (array_key_exists('configuration', $jsonConfiguration)
            && array_key_exists($configuration, $jsonConfiguration['configuration'])
            && array_key_exists('defaultValue', $jsonConfiguration['configuration'][$configuration])) {
            return $jsonConfiguration['configuration'][$configuration]['defaultValue'];
        }
        throw new CronjobConfigurationNotFoundException('Cronjob configuration in $scope "' . $scope
                                                        . '" with $configuration "' . $configuration
                                                        . '" was not found');
    }
}
