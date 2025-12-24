<?php
/* --------------------------------------------------------------
   UserFriendlyErrorHandler.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UserFriendlyErrorHandler
 */
final class UserFriendlyErrorHandler
{
    /**
     * Path to the flag file, that indicates, that the user friendly error messages system is active.
     *
     * @var string
     */
    public static $userFriendlyErrorMessagesActiveFlagFile = __DIR__
                                                             . '/../../../cache/user_friendly_error_messages_active.flag';
    
    
    /**
     * Handles an occurred error.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     *
     * @return string Generated error code
     */
    public static function handleError($p_errno, $p_errstr, $p_errfile, $p_errline, $p_errcontext)
    {
        $errorCode = self::generateErrorCode($p_errno);
        
        // Load configuration
        if (file_exists(__DIR__ . '/../../../includes/local/configure.php')) {
            require_once __DIR__ . '/../../../includes/local/configure.php';
        } else {
            require_once __DIR__ . '/../../../includes/configure.php';
        }
        
        // Determine error page URL
        $redirectUrl = HTTP_SERVER . DIR_WS_CATALOG . 'error.php?code=' . urlencode($errorCode);
        if (ENABLE_SSL === 'true' || ENABLE_SSL === true) {
            $redirectUrl = HTTPS_SERVER . DIR_WS_CATALOG . 'error.php?code=' . urlencode($errorCode);
        }
        
        // Redirect to error page
        header("HTTP/1.1 307	Temporary Redirect");
        header('Cache-Control: no-cache');
        header('Location: ' . $redirectUrl);
        
        return $errorCode;
    }
    
    
    /**
     * Generates a uniq error code.
     *
     * @param int $errno
     *
     * @return string
     */
    protected static function generateErrorCode($errno)
    {
        return time() . '-' . $errno . '-' . uniqid();
    }
    
    
    /**
     * Returns true, if the system for user friendly error messages is active, otherwise false.
     *
     * @return bool
     */
    public static function isActive()
    {
        return file_exists(self::$userFriendlyErrorMessagesActiveFlagFile)
               && !file_exists(__DIR__ . '/../../../.dev-environment');
    }
}
