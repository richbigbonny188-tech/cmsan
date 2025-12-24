<?php
/* --------------------------------------------------------------
	GeschaeftskundenversandLogger.inc.php 2020-04-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

use Gambio\Core\Logging\LoggerBuilder;

/**
 * Class GeschaeftskundenversandLogger
 *
 * @category   System
 * @package    Extensions
 * @subpackage Geschaeftskundenversand
 */
class GeschaeftskundenversandLogger
{
    /**
     * Log file name
     */
    protected const LOG_FILE = 'shipping.geschaeftskundenversand';
    
    /**
     * Debug log file name
     */
    protected const LOG_FILE_DEBUG = 'shipping.geschaeftskundenversand-debug';
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $debugLogger;
    
    
    /**
     * GeschaeftskundenversandLogger constructor.
     */
    public function __construct()
    {
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        $loggerBuilder->omitRequestData();
        
        $this->logger      = $loggerBuilder->changeNamespace(static::LOG_FILE)->build();
        $this->debugLogger = $loggerBuilder->changeNamespace(static::LOG_FILE_DEBUG)->build();
    }
    
    
    public function notice($message)
    {
        $this->logger->notice($message);
    }
    
    
    public function noticeDebug($message)
    {
        $this->debugLogger->notice($message);
    }
}
