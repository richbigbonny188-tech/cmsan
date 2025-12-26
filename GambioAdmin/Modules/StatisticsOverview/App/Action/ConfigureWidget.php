<?php
/*--------------------------------------------------------------
   ConfigureWidget.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\App\Action;

use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class representing the handler for the action of configuring a widget.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\App\Action
 * @codeCoverageIgnore
 */
class ConfigureWidget extends AbstractAction
{
    /**
     * Service.
     *
     * @var StatisticsOverviewService
     */
    private $service;
    
    
    /**
     * Constructor.
     *
     * @param StatisticsOverviewService $service Service.
     */
    public function __construct(StatisticsOverviewService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->service->configureWidget($request->getAttribute('id'),
                                        (array)json_decode($request->getBody()->getContents()));
        
        return $response->withStatus(200);
    }
}