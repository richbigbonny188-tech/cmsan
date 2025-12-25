<?php
/* --------------------------------------------------------------
 DSGVOLogger.php 2020-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DSGVO\App\Data;

use DateTime;
use DateTimeZone;
use Exception;
use Gambio\Admin\Modules\DSGVO\Models\Interfaces\AdminLog;
use Gambio\Admin\Modules\DSGVO\Services\DSGVOLogger as Logger;

/**
 * Class DSGVOLogger
 * @package Gambio\Admin\Modules\DSGVO\App\Data
 */
class DSGVOLogger implements Logger
{
    /**
     * @inheritDoc
     */
    public function logAdminActivity(AdminLog $adminLog): void
    {
        $message = $this->createLogMessage($adminLog);
        $logger  = \Gambio\Core\Logging\logger('admin_activity');
        
        $logger->info($message);
    }
    
    
    /**
     * Creates the admin activity log message.
     *
     * @param AdminLog $adminLog
     *
     * @return string
     */
    private function createLogMessage(AdminLog $adminLog): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        
        return <<<FOO
Date and time : {$this->getDateTime()}
Session ID    : {$adminLog->sessionId()}

Admin name    : {$adminLog->firstName()} {$adminLog->lastName()}
Admin email   : {$adminLog->email()}
Admin ID      : {$adminLog->customerId()}
Admin IP      : $ip

Requests      : {$_SERVER['REQUEST_METHOD']}
Parameters    : {$_SERVER['QUERY_STRING']}

URL           : {$this->getUrl()}

----------------------------------------------------------------------------------------------------------------
FOO;
    }
    
    
    /**
     * Returns a date time string.
     *
     * @return string
     */
    private function getDateTime(): string
    {
        try {
            $date = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
            
            return $date->format('d.m.Y H:i:s');
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            return date('d.m.Y H:i:s');
            // @codeCoverageIgnoreEnd
        }
    }
    
    
    /**
     * Returns an url string from server global.
     *
     * @return string Url string.
     */
    private function getUrl(): string
    {
        return (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}