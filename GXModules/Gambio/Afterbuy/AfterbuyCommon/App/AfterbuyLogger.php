<?php
/* --------------------------------------------------------------
   AfterbuyLogger.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\App;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\AfterbuyGlobal\AfterbuyGlobalReader;
use Psr\Log\LoggerInterface;
use function Gambio\Core\Logging\logger;

/**
 * Class AfterbuyLogger
 *
 * @package GXModules\Gambio\Afterbuy\AfterbuyCommon\App
 */
class AfterbuyLogger implements LoggerInterface
{
    public const LOGFILE = 'afterbuy';
    
    
    private const logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
    
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    
    /**
     * @var string
     */
    private string $minLogLevel;
    
    
    /**
     * AfterbuyLogger constructor.
     *
     * @param AfterbuyGlobalReader $reader
     */
    public function __construct(AfterbuyGlobalReader $reader)
    {
        $this->logger      = logger(self::LOGFILE);
        $this->minLogLevel = $reader->fetchLogLevel();
    }
    
    
    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function alert($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function critical($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function error($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function warning($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function notice($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function info($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function debug($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    
    
    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = []): void
    {
        $messageLevel = (int)array_search($level, self::logLevels, true);
        $minLevel     = (int)array_search($this->minLogLevel, self::logLevels, true);
        
        if ($messageLevel >= $minLevel) {
            $this->logger->log($level, $message, $context);
        }
    }
}