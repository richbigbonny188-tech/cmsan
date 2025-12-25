<?php
/* --------------------------------------------------------------
   AfterbuyLogger.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Logging\LoggerBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AfterbuyException
 *
 * @package GXModules\Gambio\Afterbuy\Classes
 */
class AfterbuyLogger implements LoggerInterface
{
    public const LOGFILE = 'afterbuy';
    
    
    private const logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
    
    
    /**
     * @var string
     */
    private string $minLogLevel;
    
    
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;
    
    
    /**
     * @param string|null $minimumLogLevel
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(?string $minimumLogLevel = null)
    {
        $this->logger = static::makeLogger();
        if ($minimumLogLevel === null) {
            $configuration     = new GambioAfterbuyConfigurationStorage();
            $this->minLogLevel = $configuration->get('minimum_log_level');
        } else {
            $this->minLogLevel = $minimumLogLevel;
        }
    }
    
    
    /**
     * @param string|null $minimumLogLevel
     *
     * @return LoggerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function createLogger(?string $minimumLogLevel = null): LoggerInterface
    {
        return new static($minimumLogLevel);
    }
    
    
    /**
     * @return LoggerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function makeLogger(): LoggerInterface
    {
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        
        return $loggerBuilder->omitRequestData()->changeNamespace(static::LOGFILE)->build();
    }
    
    
    /**
     * @param $message
     * @param $context
     *
     * @return void
     */
    public function notice($message, $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @param       $level
     * @param       $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $messageLevel = (int)array_search($level, static::logLevels, true);
        $minLevel     = (int)array_search($this->minLogLevel, static::logLevels, true);
        //$message = "|$minLevel|$messageLevel|{$this->minLogLevel}| $message";
        if ($messageLevel >= $minLevel) {
            $this->logger->log($level, $message, $context);
        }
    }
}
