<?php
/* --------------------------------------------------------------
   SingleSignonLogger.inc.php 2020-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Logging\LoggerBuilder;
use Psr\Log\LoggerInterface;

/**
 * PSR-3 logger for SingleSignOn
 *
 * Class SingleSignonLogger
 */
class SingleSignonLogger implements LoggerInterface
{
    public const LOG_FILE = 'singlesignon';
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    
    public function __construct($enabled = true, $subsystem = '')
    {
        $logFile      = static::LOG_FILE . (empty($subsystem) ? '' : '.' . $subsystem);
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        $this->logger = $loggerBuilder->omitRequestData()->changeNamespace($logFile)->build();
    }
    
    
    /**
     * Logs a debug message
     *
     * @param       $message
     * @param array $context
     */
    public function debug($message, $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function emergency($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function alert($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function critical($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function error($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function warning($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function notice($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function info($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    public function log($level, $message, array $context = [])
    {
        $message = $this->interpolate($message, $context);
        $this->logger->{__FUNCTION__}($level, $message);
    }
    
    
    /**
     * Interpolates context values into the message placeholders.
     *
     * @param       $message
     * @param array $context
     *
     * @return string
     */
    protected function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        
        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
