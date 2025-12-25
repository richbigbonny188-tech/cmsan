<?php
/* --------------------------------------------------------------
   GzipHandler.php 2023-03-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Logging\Handler;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class GzipHandler
 *
 * @package Gambio\Core\Logging\Handler
 * @codeCoverageIgnore
 */
class GzipHandler extends StreamHandler
{
    /**
     * @var int
     */
    private $maxLogfileSize;
    
    
    /**
     * GzipHandler constructor.
     *
     * @param string   $logfile        Path for the logfile
     * @param int      $level          The minimum logging level at which this handler will be triggered
     * @param bool     $bubble         Whether the messages that are handled can bubble up the stack or not
     * @param int|null $filePermission Optional file permissions (default (0644) are only for owner read/write)
     * @param bool     $useLocking     Try to lock log file before doing any writes
     * @param int      $maxLogfileSize Max file size for log files in bytes
     *
     * @throws Exception If a missing directory is not buildable
     */
    public function __construct(
        string $logfile,
        int    $level = Logger::DEBUG,
        bool   $bubble = true,
        int    $filePermission = null,
        bool   $useLocking = false,
        int    $maxLogfileSize = 1024 * 1024
    ) {
        parent::__construct($logfile, $level, $bubble, $filePermission, $useLocking);
        
        $this->maxLogfileSize = $maxLogfileSize;
    }
    
    
    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        if (file_exists($this->url) && @filesize($this->url) >= $this->maxLogfileSize) {
            $currentLogfile = fopen($this->url, 'r+');
            if ($currentLogfile !== false && flock($currentLogfile, LOCK_EX | LOCK_NB, $wouldBlock) === true
                && !$wouldBlock) {
                $currentLogfileContent = file_get_contents($this->url);
                ftruncate($currentLogfile, 0);
                fclose($currentLogfile);
                $zippedLogfileContent = gzencode($currentLogfileContent, 9);
                $zippedLogfilePath    = $this->url . '.' . date('Y-m-d_H-i-s') . '.gz';
                $zippedLogfile        = fopen($zippedLogfilePath, 'w+');
                fwrite($zippedLogfile, $zippedLogfileContent);
                fclose($zippedLogfile);
            }
        }
        
        if ($this->isJsonLogfile()) {
            $record['formatted'] = $this->prepareRecord($record['formatted']);
            
            if ($this->logfileDoesNotExist() || $this->logfileIsEmpty()) {
                $record['formatted'] = $this->prepareRecordForFirstWriteInLogfile($record['formatted']);
            } else {
                $this->prepareLogfileForAppending();
            }
        }
        
        parent::write($record);
    }
    
    
    /**
     * @return bool
     */
    private function isJsonLogfile(): bool
    {
        return $this->getFormatter() instanceof \Monolog\Formatter\JsonFormatter;
    }
    
    
    /**
     * @param string $string
     *
     * @return string
     */
    private function removeLinebreakAtEndOfString(string $string): string
    {
        return $string[strlen($string) - 1] === "\n" ? substr($string,
                                                              0,
                                                              -1) : $string;
    }
    
    
    /**
     * @return bool
     */
    private function logfileIsEmpty(): bool
    {
        clearstatcache(true);
        
        return filesize($this->url) === 0;
    }
    
    
    /**
     * @return bool
     */
    private function logfileDoesNotExist(): bool
    {
        clearstatcache(true);
        
        return !file_exists($this->url);
    }
    
    
    /**
     *
     * @return void
     */
    private function prepareLogfileForAppending(): void
    {
        $logfileContent = file_get_contents($this->url);
        
        if ($logfileContent === false) {
            return;
        }
        
        $logfileContent = substr($logfileContent, 0, -1);
        file_put_contents($this->url, $logfileContent . ",");
    }
    
    
    /**
     * @param string $record
     *
     * @return string
     */
    private function prepareRecord(string $record): string
    {
        $record = $this->removeLinebreakAtEndOfString($record);
        
        return $record . "]";
    }
    
    
    /**
     * @param string $record
     *
     * @return string
     */
    private function prepareRecordForFirstWriteInLogfile(string $record): string
    {
        return "[" . $record;
    }
}