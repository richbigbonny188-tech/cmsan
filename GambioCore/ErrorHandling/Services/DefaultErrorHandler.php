<?php
/* --------------------------------------------------------------
   DefaultErrorHandler.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\Services;

use Throwable;

/**
 * Class DefaultErrorHandler
 *
 * @package Gambio\Core\ErrorHandling
 */
interface DefaultErrorHandler
{
    /**
     * @param Throwable $exception
     */
    public function handleException(Throwable $exception): void;
    
    
    /**
     * @param int    $errorCode
     * @param string $errorMessage
     * @param string $errorFile
     * @param int    $errorLine
     * @param array  $errorContext
     *
     * @return bool
     */
    public function handleError(
        int $errorCode,
        string $errorMessage,
        string $errorFile,
        int $errorLine,
        array $errorContext
    ): bool;
    
    
    /**
     * Check for last error and handles it. This needs to be down for some fatal errors like execution time out etc.
     */
    public function shutdown(): void;
}