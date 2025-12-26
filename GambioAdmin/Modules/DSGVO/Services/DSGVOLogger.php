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

namespace Gambio\Admin\Modules\DSGVO\Services;

use Gambio\Admin\Modules\DSGVO\Models\Interfaces\AdminLog;

/**
 * Interface DSGVOLogger
 * @package Gambio\Admin\Modules\DSGVO\Services
 */
interface DSGVOLogger
{
    /**
     * Logs the admin activity information.
     *
     * @param AdminLog $adminLog
     */
    public function logAdminActivity(AdminLog $adminLog): void;
}