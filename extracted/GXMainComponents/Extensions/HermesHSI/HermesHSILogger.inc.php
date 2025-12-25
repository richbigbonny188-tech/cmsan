<?php
/* --------------------------------------------------------------
   HermesHSILogger.inc.php 2020-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Gambio\Core\Logging\LoggerBuilder;
use Psr\Log\LoggerInterface;

class HermesHSILogger implements LoggerInterface
{
    public const LOG_FILE = 'hermeshsi';
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    public function __construct()
    {
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        $this->logger = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE)->build();
    }
    
    
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Performs PSR-3 compliant context replacement.
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    protected function replaceContext(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $placeHolder => $content) {
            if (!is_array($content) && (!is_object($content) || method_exists($content, '__toString'))) {
                $replace['{' . $placeHolder . '}'] = $content;
            }
        }
        
        return strtr($message, $replace);
    }
    
    
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function alert($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function critical($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function error($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function warning($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function notice($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function info($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function debug($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        $message = $this->replaceContext($message, $context);
        $this->logger->{__FUNCTION__}($level, $message);
    }
}
