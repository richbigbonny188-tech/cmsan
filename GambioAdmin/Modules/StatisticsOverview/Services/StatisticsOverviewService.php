<?php
/*--------------------------------------------------------------
   StatisticsOverviewService.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\OverviewWidgets;

/**
 * Interface representing domain's service.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services
 */
interface StatisticsOverviewService
{
    /**
     * Get widgets by category.
     *
     * @param string $category Widget's category.
     *
     * @return OverviewWidgets Widgets.
     */
    public function getWidgetsByCategory(string $category): OverviewWidgets;
    
    
    /**
     * Save widget options.
     *
     * @param string $id      Widget's ID.
     * @param array  $options Widget's options.
     */
    public function configureWidget(string $id, array $options): void;
}