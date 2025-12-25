<?php
/* --------------------------------------------------------------
   Cronjob.inc.php 2018-09-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Cronjob
 */
class Cronjob implements CronjobInterface
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var \CronjobConfigurationCollection
     */
    protected $configuration;
    
    /**
     * @var \CronjobSettings
     */
    protected $settings;
    
    
    /**
     * Cronjob constructor.
     *
     * @param \StringType                     $name          Name of cronjob.
     * @param \StringType                     $title         Cronjob's title.
     * @param \CronjobConfigurationCollection $configuration Configurations of cronjob.
     */
    public function __construct(StringType $name, StringType $title, CronjobConfigurationCollection $configuration)
    {
        $this->name          = $name->asString();
        $this->title         = $title->asString();
        $this->configuration = $configuration;
    }
    
    
    /**
     * Named constructor of Cronjob.
     *
     * @param string                          $name          Name of cronjob.
     * @param string                          $title         Cronjob's title.
     * @param \CronjobConfigurationCollection $configuration Configurations of cronjob.
     *
     * @param \CronjobSettings                $settings
     *
     * @return \Cronjob New instance.
     * @internal param \CronjobSettings $cronjobSettings
     *
     */
    public static function create($name, $title, CronjobConfigurationCollection $configuration)
    {
        $name  = new StringType($name);
        $title = new StringType($title);
        
        return MainFactory::create(static::class, $name, $title, $configuration);
    }
    
    
    /**
     * Returns the cronjob title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    
    /**
     * Returns the cronjob name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the cronjob interval.
     *
     * @return string
     */
    public function getInterval()
    {
        return $this->getConfiguration()->getInterval()->getValue();
    }
    
    
    /**
     * Returns the cronjob status.
     *
     * @param \ExistingDirectory $cache Absolute path to cache directory.
     *
     * @return string
     */
    public function getStatus(\ExistingDirectory $cache)
    {
        if (!$this->getConfiguration()->getActive()->getValue()) {
            return 'inactive';
        }
        
        if ($this->_isLastRunSuccessful($cache)) {
            return 'active';
        }
        
        if ($this->_isTaskNotExecutedYet($cache)) {
            return 'not_executed';
        }
        
        return 'error';
    }
    
    
    /**
     * Returns the cronjob log.
     *
     * @param \ExistingDirectory $logDir Absolute path to log files directory.
     *
     * @return string
     * @throws \CronjobLogFileNotFoundException If no cronjob log file was found.
     */
    public function getLog(\ExistingDirectory $logDir)
    {
        $logPattern = $this->_camelCase2Snake($this->getName()) . '_cronjob*.log';
        $logFiles   = glob($logDir->getAbsolutePath() . DIRECTORY_SEPARATOR . $logPattern);
        if (count($logFiles) === 0) {
            $languageTextManager = MainFactory::create('LanguageTextManager', 'cronjobs');
            
            throw new CronjobLogFileNotFoundException(sprintf($languageTextManager->get_text('error_log_not_found'),
                                                              $this->getName()));
        }
        $newestLog = array_pop($logFiles);
        
        return file_get_contents($newestLog);
    }
    
    
    /**
     * Returns the cronjob configuration.
     *
     * @return \CronjobConfigurationCollection
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    
    /**
     * Returns the cronjob data as array.
     *
     * @param \ExistingDirectory $cacheDir
     *
     * @return array
     */
    public function toArray(ExistingDirectory $cacheDir)
    {
        return [
            'name'          => $this->getName(),
            'title'         => $this->getTitle(),
            'status'        => $this->getStatus($cacheDir),
            'interval'      => $this->getInterval(),
            'configuration' => $this->getConfiguration()->toArray()
        ];
    }
    
    
    /**
     * Returns if the last run was successful.
     *
     * @param \ExistingDirectory $cache Absolute path to cache directory.
     *
     * @return bool
     */
    protected function _isLastRunSuccessful(ExistingDirectory $cache)
    {
        $lastRunDate                  = $this->_getLastRunDate($cache);
        $lastSuccessDate              = $this->_getLastSuccessDate($cache);
        $notExistentDateTime          = new DateTime('1000-01-01 00:00:00');
        $notExistentDateTimeTimeStamp = $notExistentDateTime->getTimestamp();
        
        return ($lastSuccessDate >= $lastRunDate && $lastRunDate->getTimestamp() !== $notExistentDateTimeTimeStamp
                && $lastSuccessDate->getTimestamp() !== $notExistentDateTimeTimeStamp);
    }
    
    
    /**
     * Returns whether the task was executed yet or not
     *
     * @param $cache \ExistingDirectory $cache Absolute path to cache directory.
     *
     * @return bool
     */
    protected function _isTaskNotExecutedYet($cache)
    {
        $flagPathRunExists = !file_exists($cache->getAbsolutePath() . '/cronjobs/last_run-'
                                          . $this->_camelCase2Snake($this->getName()));
        
        return $flagPathRunExists;
    }
    
    
    /**
     * Returns the DateTime of the last run.
     *
     * @param \ExistingDirectory $cache Absolute path to cache directory.
     *
     * @return \DateTime
     */
    protected function _getLastRunDate(ExistingDirectory $cache)
    {
        $flagPath = $cache->getAbsolutePath() . '/cronjobs/last_run-' . $this->_camelCase2Snake($this->getName());
        
        return $this->_getDateTimeFromFlagFile(new StringType($flagPath));
    }
    
    
    /**
     * Returns the DateTime of the last successful run.
     *
     * @param \ExistingDirectory $cache Absolute path to cache directory.
     *
     * @return \DateTime
     */
    protected function _getLastSuccessDate(ExistingDirectory $cache)
    {
        $flagPath = $cache->getAbsolutePath() . '/cronjobs/last_success-' . $this->_camelCase2Snake($this->getName());
        
        return $this->_getDateTimeFromFlagFile(new StringType($flagPath));
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
     * Converts a "CamelCaseString" into a "snake_case_string".
     *
     * @param string $input Value to be formatted.
     *
     * @return string Snake case string of $input.
     */
    protected function _camelCase2Snake($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        
        return implode('_', $ret);
    }
}