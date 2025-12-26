<?php
/* --------------------------------------------------------------
 InvalidPreferredPeriodException.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Exceptions;

use Exception;

class InvalidPreferredPeriodException extends Exception
{
    /**
     * Create instance.
     */
    public static function forPeriod(string $period): InvalidPreferredPeriodException
    {
        return new self("$period is not a valid period");
    }
}