<?php
/* --------------------------------------------------------------
	ParcelShopFinderLogger.inc.php 2020-04-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

use Gambio\Core\Logging\LoggerBuilder;

/**
 * Class ParcelShopFinderLogger
 *
 * @category   System
 * @package    Extensions
 * @subpackage ParcelShopFinder
 */
class ParcelShopFinderLogger
{
    /**
     * Log file name
     */
    const LOG_FILE = 'shipping.parcelshopfinder';
    

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    
    /**
     * ParcelShopFinderLogger constructor.
     */
    public function __construct()
    {
        /** @var LoggerBuilder $loggerBuilder */
        $loggerBuilder = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
        $this->logger = $loggerBuilder->omitRequestData()->changeNamespace(static::LOG_FILE)->build();
    }
    
    
    /**
     * @param $message
     */
    public function notice($message)
    {
        $this->logger->notice($message);
    }
    
    
    /**
     * @param $message
     */
    public function noticeDebug($message)
    {
        $this->logger->debug($message);
    }
}
