<?php
/* --------------------------------------------------------------
   CronjobTaskRepository.inc.php 2021-09-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobTaskRepository
 */
class CronjobTaskRepository implements CronjobTaskRepositoryInterface
{
    /**
     * @var \CronjobSettings
     */
    protected $settings;
    
    protected $storage;
    
    
    /**
     * CronjobTaskRepository constructor.
     *
     * @param \CronjobSettings             $settings
     * @param \CronjobConfigurationStorage $storage
     */
    public function __construct(CronjobSettings $settings, CronjobConfigurationStorage $storage)
    {
        $this->settings = $settings;
        $this->storage  = $storage;
    }
    
    
    /**
     * Returns all cronjob tasks.
     *
     * @return \CronjobTaskCollection
     * @throws \CronjobConfigurationNotFoundException
     */
    public function getAll()
    {
        $tasks    = [];

        $iterator = new IteratorIterator(new DirectoryIterator($this->settings->configurationDirectory()));
        foreach ($iterator as $configFile) {
            /** @var \SplFileInfo $configFile */
            if ($this->_isCronjobConfiguration($configFile)) {
                $cronjobTask = $this->createCronjobTaskFromConfigFile($configFile);
                if ($cronjobTask !== false) {
                    $tasks[] = $cronjobTask;
                }
            }
        }
    
        $gxmodulesIterator = new \GlobIterator(DIR_FS_CATALOG . '/GXModules/*/*/Admin/CronjobConfiguration/*.json');
        /** @var SplFileInfo $gxmodulesFile */
        foreach ($gxmodulesIterator as $gxmodulesFile) {
            $gxmodulesCronjobTask = $this->createCronjobTaskFromConfigFile($gxmodulesFile);
            if ($gxmodulesCronjobTask !== false) {
                $tasks[] = $gxmodulesCronjobTask;
            }
        }
        
        return CronjobTaskCollection::collect(...$tasks);
    }
    
    
    /**
     * Instantiates a CronjobTask based on its configuration JSON file. 
     * 
     * @param SplFileInfo $configFile
     *
     * @return false|mixed
     * @throws CronjobConfigurationNotFoundException
     */
    protected function createCronjobTaskFromConfigFile(SplFileInfo $configFile)
    {
        $cronjobName = str_replace('.json', '', $configFile->getFilename());
    
        $active = new BoolType($this->storage->get($cronjobName, 'active'));
        if ($active->asBool()) {
            $loggerName       = $cronjobName . 'CronjobLogger';
            $dependenciesName = $cronjobName . 'CronjobDependencies';
            $taskName         = $cronjobName . 'CronjobTask';
            $logger           = MainFactory::create($loggerName, new ExistingDirectory($this->settings->getRoot()));
            $dependencies     = MainFactory::create($dependenciesName, $this->storage);
            $schedule         = new StringType($this->storage->get($cronjobName, 'interval'));
            $sortOrder        = new IntType($this->storage->get($cronjobName, 'sortOrder'));
    
            return MainFactory::create($taskName, $schedule, $sortOrder, $logger, $dependencies);
        }
        return false;
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
}