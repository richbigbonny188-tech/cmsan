<?php
/* --------------------------------------------------------------
   DebugDataProvider.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App;

/**
 * Class DebugDataProvider
 *
 * @package Gambio\Core\ErrorHandling\App
 */
class DebugDataProvider
{
    /**
     * @param int $skippedItems
     *
     * @return array
     */
    public function getDebugTrace(int $skippedItems = 1): array
    {
        $traceLog = [];
        foreach (debug_backtrace() as $index => $trace) {
            if ($index <= $skippedItems - 1) {
                continue;
            }
            unset($trace['object'], $trace['type'], $trace['args']);
            
            $traceLog[] = $trace;
        }
        
        return $traceLog;
    }
    
    
    /**
     * @param string $file
     * @param int    $line
     * @param int    $range
     *
     * @return string
     */
    public function getCodeSnippet(string $file, int $line, int $range = 2): string
    {
        $snippet = [];
        $lines   = file($file);
        
        for ($i = $range; $i > 0; $i--) {
            if (isset($lines[$line - $i - 1])) {
                $snippet[] = '│  ' . $lines[$line - $i - 1];
            }
        }
        
        $snippet[] = '├─ ' . $lines[$line - 1];
        
        for ($i = 1; $i <= $range; $i++) {
            if (isset($lines[$line + $i - 1])) {
                $snippet[] = '│  ' . $lines[$line + $i - 1];
            }
        }
        
        $snippet = array_map(static function (string $line): string {
            return rtrim($line, PHP_EOL);
        },
            $snippet);
        
        return implode(PHP_EOL, $snippet);
    }
}