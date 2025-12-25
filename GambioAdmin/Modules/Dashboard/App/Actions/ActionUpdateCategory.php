<?php
/* --------------------------------------------------------------
  ActionUpdateCategory.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\App\Actions;

use Gambio\Admin\Modules\DashboardStatistics\Services\DashboardStatisticsService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class ActionUpdateCategory
 *
 * @package Gambio\Admin\Modules\Dashboard\App\Actions
 * @codeCoverageIgnore
 */
class ActionUpdateCategory extends AbstractAction
{
    
    /**
     * @var DashboardStatisticsService
     */
    private $statisticsService;
    
    
    /**
     * ActionUpdateCategory constructor.
     *
     * @param DashboardStatisticsService $statisticsService Dashboard widget service.
     */
    public function __construct(DashboardStatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        if (is_string($request->getAttribute('category'))) {
            $this->statisticsService->updatePreferredCategory($request->getAttribute('category'));
        }
        
        return $response;
    }
}