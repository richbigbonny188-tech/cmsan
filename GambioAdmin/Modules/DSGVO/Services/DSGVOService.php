<?php
/* --------------------------------------------------------------
 DSGVOService.php 2020-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DSGVO\Services;

/**
 * Interface DSGVOService
 * @package Gambio\Admin\Modules\DSGVO\Services
 */
interface DSGVOService
{
    /**
     * Logs admin activity.
     */
    public function logAdminActivity(): void;
    
    
    /**
     * Checks if the admin activity logging is enabled.
     *
     * @return bool
     */
    public function isAdminLoggingEnabled(): bool;
}