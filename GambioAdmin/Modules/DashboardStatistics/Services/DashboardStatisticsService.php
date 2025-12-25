<?php
/*--------------------------------------------------------------
   DashboardStatisticsService.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Services;

use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredCategory;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredPeriod;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Result;

interface DashboardStatisticsService
{
    /**
     * Get statistics for the Gambio Admin widget.
     */
    public function getStatistics(): Result;
    
    
    /**
     * Update preferred period for current user.
     */
    public function updatePreferredPeriod(string $period): void;
    
    
    /**
     * Get preferred period for current user.
     */
    public function getPreferredPeriod(): ?PreferredPeriod;
    
    
    /**
     * Update preferred category for current user.
     */
    public function updatePreferredCategory(string $category): void;
    
    
    /**
     * Get preferred category for current user.
     */
    public function getPreferredCategory(): ?PreferredCategory;
}