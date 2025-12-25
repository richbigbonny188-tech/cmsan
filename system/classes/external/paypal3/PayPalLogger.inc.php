<?php
/* --------------------------------------------------------------
	PayPalLogger.inc.php 2020-04-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

use Gambio\Core\Logging\LoggerBuilder;
use Psr\Log\LoggerInterface;

/**
 * central logging class for PayPal3.
 * Uses LogControl where available, falls back to FileLog otherwise
 */
class PayPalLogger implements LoggerInterface
{
    /**
     * LogControl group
     */
    protected const LOG_GROUP = 'payment';
    
    /**
     * Log file name
     */
    protected const LOG_FILE = 'payment.paypal3';
    
    /**
     * Debug log file name
     */
    protected const LOG_FILE_DEBUG = 'payment.paypal3-debug';
    
    /**
     * Installments Upstream Presentment log file name
     */
    protected const LOG_FILE_IUP = 'payment.paypal3-iup';
    
    /**
     * PayPalConfigurationStorage instance
     */
    protected $configuration;
    
    /** @var LoggerInterface */
    protected $logger;
    
    /** @var LoggerInterface */
    protected $loggerDebug;
    
    /** @var LoggerInterface */
    protected $loggerIup;
    
    
    /**
     * constructor; initializes logging mechanism (LogControl/FileLog) and configuration
     */
    public function __construct()
    {
        $this->configuration = MainFactory::create('PayPalConfigurationStorage');
    
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder     = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        $this->logger      = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE)->build();
        $this->loggerDebug = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE_DEBUG)->build();
        $this->loggerIup   = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE_IUP)->build();
    }
    
    
    /**
     * logs an error message
     *
     * @param string $message message to be logged
     * @param array  $context
     */
    public function error($message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
    
    
    /**
     * logs a warning message
     *
     * @param string $message message to be logged
     * @param array  $context
     */
    public function warning($message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
    
    
    /**
     * logs a notice message
     *
     * @param string $message message to be logged
     * @param array  $context
     */
    public function notice($message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }
    
    
    /**
     * logs a message for Installments Upstream Presentment
     *
     * @param       $message
     * @param array $context
     */
    public function iup_notice($message, array $context = []): void
    {
        $this->loggerIup->notice($message, $context);
    }
    
    
    /**
     * logs a debug message.
     * Debug messages are used for extended logging; this will log all API traffic
     *
     * @param string $message message to be logged
     * @param array  $context
     */
    public function debug_notice($message, array $context = []): void
    {
        if ((bool)$this->configuration->get('debug_logging') !== true) {
            return;
        }
        $this->loggerDebug->notice($message, $context);
    }
    
    
    public function emergency($message, array $context = [])
    {
        $this->logger->{__FUNCTION__}($message, $context);
    }
    
    
    public function alert($message, array $context = [])
    {
        $this->logger->{__FUNCTION__}($message, $context);
    }
    
    
    public function critical($message, array $context = [])
    {
        $this->logger->{__FUNCTION__}($message, $context);
    }
    
    
    public function info($message, array $context = [])
    {
        $this->logger->{__FUNCTION__}($message, $context);
    }
    
    
    public function debug($message, array $context = [])
    {
        $this->logger->{__FUNCTION__}($message, $context);
    }
    
    
    public function log($level, $message, array $context = [])
    {
        $this->logger->{__FUNCTION__}($level, $message, $context);
    }
}
