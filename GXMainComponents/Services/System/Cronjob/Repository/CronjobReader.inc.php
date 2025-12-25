<?php
/* --------------------------------------------------------------
   CronjobReader.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

/**
 * Class CronjobReader
 */
class CronjobReader implements CronjobReaderInterface
{
    /**
     * @var \CronjobMetaInterface
     */
    protected $meta;
    
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \CronjobSettings
     */
    protected $settings;
    
    /**
     * @var ConfigurationStorageRepository
     */
    protected $configStorage;
    
    
    /**
     * CronjobRepository constructor.
     *
     * @param \CI_DB_query_builder                  $db
     * @param \CronjobSettings                      $settings
     * @param ConfigurationStorageRepositoryBuilder $storageBuilder
     */
    public function __construct(
        CI_DB_query_builder $db,
        CronjobSettings $settings,
        ConfigurationStorageRepositoryBuilder $storageBuilder
    ) {
        $this->db            = $db;
        $this->settings      = $settings;
        $this->configStorage = $storageBuilder->build('cronjobs');
    }
    
    
    /**
     * Returns all cronjobs.
     *
     * @return array Collected cronjobs with meta data about execution.
     */
    public function getAll()
    {
        $cronjobs = [];
        $iterator = new IteratorIterator(new DirectoryIterator($this->settings->configurationDirectory()));
        
        foreach ($iterator as $configFile) {
            /** @var \SplFileInfo $configFile */
            if ($this->_isCronjobConfiguration($configFile)) {
                $cronjobs[] = $this->_mapCronjob($configFile);
            }
        }
    
        $gxmodulesIterator = new \GlobIterator(DIR_FS_CATALOG . '/GXModules/*/*/Admin/CronjobConfiguration/*.json');
        /** @var SplFileInfo $gxmodulesFile */
        foreach ($gxmodulesIterator as $gxmodulesFile) {
            $cronjobs[] = $this->_mapCronjob($gxmodulesFile);
        }
    
        $lastRun = $this->_getDateTimeFromFlagFile(new StringType($this->settings->lastRunFlag()));
        $metaData = ['lastRun' => $lastRun, 'cronjobs' => $cronjobs];
        
        return $metaData;
    }
    
    
    /**
     * Maps cronjob data by the given config file.
     *
     * @param \SplFileInfo $configFile
     *
     * @return array
     */
    protected function _mapCronjob(SplFileInfo $configFile)
    {
        $cronjobName = str_replace('.json', '', $configFile->getFilename());
        
        $configurationJsonData = json_decode(file_get_contents($configFile->getPathname()), true);
        $cronjobConfiguration  = [];
        
        foreach ($configurationJsonData['configuration'] as $key => $configuration) {
            $cronjobConfiguration[] = $this->_mapCronjobConfiguration($cronjobName, $key, $configuration);
        }
        
        return [
            'name'          => $cronjobName,
            'title'         => $configurationJsonData['title'],
            'configuration' => $cronjobConfiguration,
        ];
    }
    
    
    /**
     * Returns the DateTime that is contained in a given file.
     *
     * @param \StringType $flagPath
     *
     * @return \DateTime
     */
    protected function _getDateTimeFromFlagFile(StringType $flagPath)
    {
        if (!file_exists($flagPath->asString())) {
            return new DateTime('1000-01-01 00:00:00');
        }
        
        return new DateTime(file_get_contents($flagPath->asString()));
    }
    
    
    /**
     * Maps cronjob configurations by their related data.
     *
     * @param string $cronjobName
     * @param string $key
     * @param array  $configuration
     *
     * @return array
     */
    protected function _mapCronjobConfiguration($cronjobName, $key, $configuration)
    {
        $configValue = $this->configStorage->get("$cronjobName/$key");
        $value       = $this->_getConfigurationValue($configuration, ['value' => $configValue]);
        
        if (array_key_exists('values', $configuration)) {
            return CronjobConfiguration::withValues(
                $key,
                $configuration['label'],
                $configuration['type'],
                $value,
                $configuration['values']
            )->toArray();
        }
        
        return CronjobConfiguration::create($key, $configuration['label'], $configuration['type'], $value)->toArray();
    }
    
    
    /**
     * Returns the configuration value.
     * If data was found in the $result, they get processed and returned.
     * Otherwise, the default from $configuration is used.
     *
     * @param array      $configuration
     * @param array|null $result
     *
     * @return bool|mixed
     */
    protected function _getConfigurationValue(array $configuration, array $result = null)
    {
        if ($result) {
            if ($result['value'] === 'true' || $result['value'] === 'false') {
                return $result['value'] === 'true';
            }
            
            return $result['value'];
        }
        
        return $configuration['defaultValue'];
    }
    
    
    /**
     * Checks if given file info is a cronjob configuration file.
     *
     * @param \SplFileInfo $configFile
     *
     * @return bool
     */
    protected function _isCronjobConfiguration(SplFileInfo $configFile)
    {
        return strpos($configFile->getFilename(), '.json') !== false;
    }
    
    
    /**
     * Returns a cronjob by the given identifier.
     *
     * @param \StringType $name Cronjob identifier.
     *
     * @return array Cronjob of given identifier.
     */
    public function getByName(StringType $name)
    {
        $configFileName = $name->asString() . '.json';
        if (file_exists($this->settings->configurationDirectory() . DIRECTORY_SEPARATOR . $configFileName)) {
            $configFile     = new SplFileInfo(
                $this->settings->configurationDirectory() . DIRECTORY_SEPARATOR . $configFileName
            );
        } else {
            $gxmodulesIterator = new \GlobIterator(DIR_FS_CATALOG . '/GXModules/*/*/Admin/CronjobConfiguration/'
                                                   . $name->asString() . '.json');
            /** @var SplFileInfo $gxmodulesFile */
            foreach ($gxmodulesIterator as $gxmodulesFile) {
                $configFile = $gxmodulesFile;
            }
        }
        
        return $this->_mapCronjob($configFile);
    }
}