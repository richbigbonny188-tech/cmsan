<?php
/* --------------------------------------------------------------
   DefaultErrorHandler.php 2023-03-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App;

use Gambio\Admin\Modules\UserFriendlyErrorPage\Services\UserFriendlyErrorPageErrorHandlerService;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\ErrorHandling\Services\DefaultErrorHandler as DefaultErrorHandlerInterface;
use Gambio\Core\Logging\LoggerBuilder;
use Throwable;
use function Gambio\Core\Application\env;

/**
 * Class DefaultErrorHandler
 *
 * Currently, this error handler is mainly focused on the shop frontend because, there is also another
 * error handler for the admin and therefore, this one is only used in the frontend.
 * At a later point, we should refactor this error handler to handle errors and exceptions for both
 * frontend and backend.
 *
 * @package Gambio\Core\ErrorHandling
 * @codeCoverageIgnore
 */
class DefaultErrorHandler implements DefaultErrorHandlerInterface
{
    /**
     * @var LoggerBuilder
     */
    private $loggerBuilder;
    
    /**
     * @var UserFriendlyErrorPageErrorHandlerService
     */
    private $userFriendlyErrorPagesService;
    
    /**
     * @var Environment
     */
    private $environment;
    
    /**
     * @var string
     */
    private $sessionId;
    
    /**
     * @var DebugDataProvider
     */
    private $debugDataProvider;
    
    
    /**
     * DefaultErrorHandler constructor.
     *
     * @param LoggerBuilder                            $loggerBuilder
     * @param UserFriendlyErrorPageErrorHandlerService $userFriendlyErrorPagesService
     * @param Environment                              $environment
     * @param DebugDataProvider                        $debugDataProvider
     */
    public function __construct(
        LoggerBuilder $loggerBuilder,
        UserFriendlyErrorPageErrorHandlerService $userFriendlyErrorPagesService,
        Environment $environment,
        DebugDataProvider $debugDataProvider
    ) {
        $this->loggerBuilder                 = $loggerBuilder->changeNamespace('error-handler');
        $this->userFriendlyErrorPagesService = $userFriendlyErrorPagesService;
        $this->environment                   = $environment;
        $this->debugDataProvider             = $debugDataProvider;
        
        $this->sessionId = uniqid('session-', true);
    }
    
    
    /**
     * @inheritDoc
     */
    public function handleException(Throwable $exception): void
    {
        http_response_code(500);
        
        $this->loggerBuilder->addRequestData()->build()->critical($exception->getMessage(),
                                                                  [
                                                                      'session ID' => $this->sessionId,
                                                                      'exception'  => $exception,
                                                                  ]);
        $this->redirectToErrorPage();
        $this->printErrorDetailsOnPage($exception);
    }
    
    
    /**
     * @inheritDoc
     */
    public function handleError(
        int $errorCode,
        string $errorMessage,
        string $errorFile,
        int $errorLine,
        array $errorContext = []
    ): bool {
        // Don't handle errors while handling errors
        set_error_handler(function (...$args): bool {return true;}, E_ALL);
        
        switch ($errorCode) {
            // handling notices
            case E_USER_NOTICE:
            case E_NOTICE:
                break;
            
            // handling errors
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_USER_ERROR:
                http_response_code(500);
                
                $this->logError($errorCode, $errorMessage, $errorFile, $errorLine, $errorContext);
                $this->redirectToErrorPage();
                break;
            
            // handling warnings
            case E_USER_DEPRECATED:
            case E_DEPRECATED:
                if (env('LOG_DEPRECATED_WARNINGS', false)) {
                    $this->logWarning($errorCode, $errorMessage, $errorFile, $errorLine, $errorContext);
                }
                break;
            case E_COMPILE_WARNING:
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_STRICT:
            default:
                $this->logWarning($errorCode, $errorMessage, $errorFile, $errorLine, $errorContext);
        }
        
        // Restore normal error handler
        restore_error_handler();
        
        return true;
    }
    
    
    /**
     * @inheritDoc
     */
    public function shutdown(): void
    {
        $latestError = error_get_last();
        if (isset($latestError)) {
            $this->handleError($latestError['type'],
                               $latestError['message'],
                               $latestError['file'],
                               $latestError['line'],
                               []);
        }
    }
    
    
    /**
     * @param int    $errorCode
     * @param string $errorMessage
     * @param string $errorFile
     * @param int    $errorLine
     * @param array  $errorContext
     */
    private function logError(
        int $errorCode,
        string $errorMessage,
        string $errorFile,
        int $errorLine,
        array $errorContext
    ): void {
        $debugTrace = $this->debugDataProvider->getDebugTrace(4);
        foreach ($debugTrace as &$items) {
            if (!isset($items['file'], $items['line'])) {
                continue;
            }
            
            $items['snippet'] = $this->debugDataProvider->getCodeSnippet($items['file'], $items['line']);
        }
        unset($items);
        
        $logData = [
            'session ID'      => $this->sessionId,
            'code'            => $errorCode,
            'file'            => $errorFile,
            'line'            => $errorLine,
            'context'         => $errorContext,
            'previous output' => @ob_get_clean(),
            'code snippet'    => $this->debugDataProvider->getCodeSnippet($errorFile, $errorLine),
            'trace'           => $debugTrace,
        ];
        
        $this->loggerBuilder->addRequestData()->build()->error($errorMessage, $logData);
    }
    
    
    /**
     * @param int    $errorCode
     * @param string $errorMessage
     * @param string $errorFile
     * @param int    $errorLine
     * @param array  $errorContext
     */
    private function logWarning(
        int $errorCode,
        string $errorMessage,
        string $errorFile,
        int $errorLine,
        array $errorContext
    ): void {
        if (!env('LOG_WARNINGS', true) || $this->shouldErrorBeSuppressed($errorFile, $errorLine)) {
            return;
        }
        
        $debugTrace = $this->debugDataProvider->getDebugTrace(4);
        foreach ($debugTrace as &$items) {
            if (!isset($items['file'], $items['line'])) {
                continue;
            }
            
            $items['snippet'] = $this->debugDataProvider->getCodeSnippet($items['file'], $items['line']);
        }
        unset($items);
        
        $logData = [
            'session ID'   => $this->sessionId,
            'code'         => $errorCode,
            'file'         => $errorFile,
            'line'         => $errorLine,
            'context'      => $errorContext,
            'code snippet' => $this->debugDataProvider->getCodeSnippet($errorFile, $errorLine),
            'trace'        => $debugTrace,
        ];
        
        $this->loggerBuilder->addRequestData()->build()->warning($errorMessage, $logData);
    }
    
    
    private function redirectToErrorPage(): void
    {
        if ($this->userFriendlyErrorPagesService->isModuleActive() && !$this->environment->isDev()) {
            $errorPageCode = $this->userFriendlyErrorPagesService->redirectToUserFriendlyErrorPage();
            $this->loggerBuilder->addRequestData()->build()->info('Showed user friendly error page: ' . $errorPageCode,
                                                                  [
                                                                      'session ID'      => $this->sessionId,
                                                                      'error page code' => $errorPageCode,
                                                                  ]);
        }
    }
    
    
    /**
     * @param Throwable $exception
     */
    private function printErrorDetailsOnPage(Throwable $exception): void
    {
        echo '<h1>Unexpected error occurred...</h1>';
        
        if ($this->environment->isDev()) {
            echo '<pre>';
            
            echo 'Message: ' . $exception->getMessage() . PHP_EOL;
            echo 'Code: ' . $exception->getCode() . PHP_EOL;
            echo 'File: ' . $exception->getFile() . PHP_EOL;
            echo 'Line: ' . $exception->getLine() . PHP_EOL . PHP_EOL;
            
            echo $this->debugDataProvider->getCodeSnippet($exception->getFile(), $exception->getLine()) . PHP_EOL
                 . PHP_EOL;
            
            echo 'Trace: ' . PHP_EOL . PHP_EOL;
            foreach ($exception->getTrace() as $index => $trace) {
                echo '[' . $index . '] ' . $trace['file'] . ':' . $trace['line'] . PHP_EOL;
                if (!isset($items['file'], $items['line'])) {
                    continue;
                }
                
                echo $this->debugDataProvider->getCodeSnippet($trace['file'], $trace['line']) . PHP_EOL . PHP_EOL;
            }
            
            echo '</pre>';
        } else {
            echo $exception->getMessage();
        }
    }
    
    
    /**
     * @param string $errorFile
     * @param int    $errorLine
     *
     * @return bool
     */
    private function shouldErrorBeSuppressed(string $errorFile, int $errorLine): bool
    {
        $suppressError = false;
        
        if (!env('LOG_SUPPRESSED_WARNINGS', false) && is_file($errorFile)) {
            $fp = @fopen($errorFile, 'r');
            if ($fp) {
                $line = 1;
                while (($buffer = fgets($fp)) !== false) {
                    // suppress error if line contains the @-operator
                    if ($line === $errorLine && strpos($buffer, '@') !== false) {
                        $suppressError = true;
                        break;
                    }
                    $line++;
                }
                fclose($fp);
            }
        }
        
        return $suppressError;
    }
}