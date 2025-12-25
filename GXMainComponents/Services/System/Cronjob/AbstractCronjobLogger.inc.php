<?php
/* --------------------------------------------------------------
   AbstractCronjobLogger.inc.php 2018-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class AbstractCronjobLogger
 */
abstract class AbstractCronjobLogger
{
    /**
     * @var Logger
     */
    protected $logger;
    
    /**
     * @var string
     */
    protected $root;
    
    
    /**
     * AbstractCronjobLogger constructor.
     *
     * @param \ExistingDirectory $root Shop root directory.
     *
     * @throws \Exception
     */
    public function __construct(ExistingDirectory $root)
    {
        $this->root   = $root->getAbsolutePath();
        $this->logger = new Logger($this->_getLoggerName());
        
        $timeInfo = date('Y-W');
        $logDir   = $this->root . DIRECTORY_SEPARATOR . 'logfiles';
        $logFile  = $this->_getLoggerNameInSnakeCase() . '-' . $timeInfo . '.log';
        
        $this->logger->pushHandler(new StreamHandler($logDir . DIRECTORY_SEPARATOR . $logFile, Logger::INFO));
    }
    
    
    /**
     * Adds a new log record.
     *
     * @param array $context (Optional) Additional information.
     *
     * @return void
     */
    abstract public function log(array $context = []);
    
    
    /**
     * Adds a new error log record.
     *
     * @param array $context (Optional) Additional information.
     *
     * @return void
     */
    abstract public function logError(array $context = []);
    
    
    /**
     * Creates a "last_run" flag cache file for the concrete logger.
     */
    public function lastRun()
    {
        $this->_createCacheFlagFile(false);
    }
    
    
    /**
     * Creates a "last_success" flag cache file for the concrete logger.
     */
    public function lastSuccess()
    {
        $this->_createCacheFlagFile();
    }
    
    
    /**
     * Creates whether the "last_success" or "last_run" flag cache file.
     *
     * @param bool $success True for "success"-flag, and false otherwise.
     */
    protected function _createCacheFlagFile($success = true)
    {
        $this->_createCronjobsCacheIfNotExists();
        $type     = $success ? 'success' : 'run';
        $dateTime = new DateTime();
        $flagName = str_replace('_cronjob', '', $this->_getLoggerNameInSnakeCase());
        
        file_put_contents($this->root . '/cache/cronjobs/last_' . $type . '-' . $flagName,
                          $dateTime->format('Y-m-d H:i:s.u'));
    }
    
    
    /**
     * Returns the name of the logger.
     * The name of the logger class without "Logger"-Suffix is used.
     *
     * @return string Name of logger.
     */
    protected function _getLoggerName()
    {
        return str_replace('Logger', '', get_class($this));
    }
    
    
    /**
     * Returns the logger name in snake case format.
     * The name of the logger class without "Logger"-Suffix is used.
     *
     * @return string Logger name in snake case.
     */
    protected function _getLoggerNameInSnakeCase()
    {
        return $this->_camelCase2Snake($this->_getLoggerName());
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
    
    
    /**
     * Creates a "cronjob" directory in the shops cache directory, if not already exists.
     */
    protected function _createCronjobsCacheIfNotExists()
    {
        $cacheDir = $this->root . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'cronjobs';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }
    }
}