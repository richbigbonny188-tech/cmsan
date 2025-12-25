<?php
/* --------------------------------------------------------------
   LoggerAdapter.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);


namespace GXModules\Gambio\Afterbuy\Admin\Classes;

use AbstractCronjobLogger;
use Psr\Log\LoggerInterface;

/**
 * Class GambioAfterbuyModuleCenterModule
 *
 * Builds a bridge between Psr\Log\LoggerInterface and AbtractCronjobLogger.
 *
 * This allows the CronjobTask to hand its logger over to other classes from outside the Gambio Cronjobs domain which
 * implement LoggerAwareInterface.
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class LoggerAdapter implements LoggerInterface
{
    /**
     * @param AbstractCronjobLogger $cronjobLogger
     */
    public function __construct(AbstractCronjobLogger $cronjobLogger)
    {
        $this->cronjobLogger = $cronjobLogger;
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
    public function notice($message, array $context = []): void
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
        if ($level === 'error') {
            $this->cronjobLogger->logError(['message' => $message, 'level' => $level]);
        } else {
            $this->cronjobLogger->log(['message' => $message, 'level' => $level]);
        }
    }
}
