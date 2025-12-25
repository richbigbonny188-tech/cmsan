<?php
/* --------------------------------------------------------------
   TextAndJsonLoggerBuilder.php 2022-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Logging\Builder;

use Exception;
use Gambio\Core\Logging\DataProcessors\AddRequestDataProcessor;
use Gambio\Core\Logging\DataProcessors\ExceptionDataProcessor;
use Gambio\Core\Logging\DataProcessors\RemoveUnnecessaryInformationDataProcessor;
use Gambio\Core\Logging\FallbackLogger;
use Gambio\Core\Logging\Formatter\TextFormatter;
use Gambio\Core\Logging\Handler\GzipHandler;
use Gambio\Core\Logging\LoggerBuilder;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\GroupHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class TextAndJsonLoggerBuilder
 *
 * @package Gambio\Core\Logging\Builder
 */
class TextAndJsonLoggerBuilder implements LoggerBuilder
{
    /**
     * @var string
     */
    private $namespace;
    
    /**
     * @var bool
     */
    private $addRequestData;
    
    
    /**
     * TextAndJsonLoggerBuilder constructor.
     *
     * @param string $namespace
     * @param bool   $addRequestData
     */
    public function __construct(string $namespace = 'general', bool $addRequestData = true)
    {
        $this->namespace      = $namespace;
        $this->addRequestData = $addRequestData;
    }
    
    
    /**
     * @inheritDoc
     */
    public function changeNamespace(string $namespace = 'general'): LoggerBuilder
    {
        $this->namespace = $namespace;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addRequestData(): LoggerBuilder
    {
        $this->addRequestData = true;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function omitRequestData(): LoggerBuilder
    {
        $this->addRequestData = false;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): LoggerInterface
    {
        $handlers = [];
        try {
            $txtHandler = new GzipHandler(__DIR__ . '/../../../logfiles/' . $this->namespace . '.log.txt');
            $txtHandler->setFormatter(new TextFormatter());
            
            $handlers[] = $txtHandler;
        } catch (Exception $e) {
        }
        
        try {
            $jsonHandler = new GzipHandler(__DIR__ . '/../../../logfiles/' . $this->namespace . '.log.json');
            $jsonHandler->setFormatter(new JsonFormatter());
            
            $handlers[] = $jsonHandler;
        } catch (Exception $e) {
        }
        
        if (count($handlers) === 0) {
            return new FallbackLogger();
        }
        
        $logger = new Logger($this->namespace);
        $logger->pushHandler(new GroupHandler($handlers));
        $logger->pushProcessor(new RemoveUnnecessaryInformationDataProcessor());
        $logger->pushProcessor(new ExceptionDataProcessor(true));
        if ($this->addRequestData) {
            $logger->pushProcessor(new AddRequestDataProcessor());
        }
        
        return $logger;
    }
}