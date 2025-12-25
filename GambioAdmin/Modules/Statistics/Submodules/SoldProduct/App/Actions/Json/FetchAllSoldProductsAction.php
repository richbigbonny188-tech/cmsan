<?php
/*--------------------------------------------------------------
   FetchAllSoldProductsAction.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Json;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductReadService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllSoldProductsAction
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Json
 * @codeCoverageIgnore
 */
class FetchAllSoldProductsAction extends AbstractAction
{
    /**
     * @param SoldProductReadService $service
     */
    public function __construct(private SoldProductReadService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $page    = (int)$request->getQueryParam('page', 1);
        $limit   = (int)$request->getQueryParam('limit', 25);
        $offset  = $limit * ($page - 1);
        
        return $response->withJson(['data' => $this->service->getSoldProducts($limit, $offset)->toArray()]);
    }
}