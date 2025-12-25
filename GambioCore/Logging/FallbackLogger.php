<?php
/* --------------------------------------------------------------
 FallbackLogger.php 2020-04-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Logging;

use Psr\Log\LoggerInterface;

/**
 * Class FallbackLogger
 * @package Gambio\Core\Logging
 * @codeCoverageIgnore
 */
class FallbackLogger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function alert($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function critical($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function error($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function warning($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function notice($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function info($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function debug($message, array $context = []): void
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = []): void
    {
    }
}