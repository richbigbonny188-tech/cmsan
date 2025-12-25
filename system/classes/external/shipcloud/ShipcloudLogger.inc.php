<?php
/* --------------------------------------------------------------
	ShipcloudLogger.inc.php 2020-04-21
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
 * central logging class for Shipcloud.
 * Uses LogControl where available, falls back to FileLog otherwise
 */
class ShipcloudLogger
{
    /**
     * Log file name
     */
    public const LOG_FILE = 'shipping.shipcloud';
    
    /**
     * Debug log file name
     */
    public const LOG_FILE_DEBUG = 'shipping.shipcloud-debug';
    
    /**
     * ShipcloudConfigurationStorage instance
     */
    protected $configuration;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var LoggerInterface
     */
    protected $loggerDebug;
    
    
    /**
     * constructor; initializes logging mechanism (LogControl/FileLog) and configuration
     */
    public function __construct()
    {
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder       = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        $this->logger        = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE)->build();
        $this->loggerDebug   = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE_DEBUG)->build();
        $this->configuration = MainFactory::create('ShipcloudConfigurationStorage');
    }
    
    
    /**
     * logs an error message
     *
     * @param string $message message to be logged
     */
    public function error($message)
    {
        $this->loggerDebug->error($message);
    }
    
    
    /**
     * logs a warning message
     *
     * @param string $message message to be logged
     */
    public function warning($message)
    {
        $this->logger->warning($message);
    }
    
    
    /**
     * logs a notice message
     *
     * @param string $message message to be logged
     */
    public function notice($message)
    {
        $this->logger->notice($message);
    }
    
    
    /**
     * logs a debug message.
     * Debug messages are used for extended logging; this will log all API traffic
     *
     * @param string $message message to be logged
     */
    public function debug_notice($message)
    {
        if (filter_var($this->configuration->get('debug_logging'), FILTER_VALIDATE_BOOLEAN) === true) {
            $this->loggerDebug->notice($message);
        }
    }
}

