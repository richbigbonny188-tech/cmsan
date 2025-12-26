<?php
/*--------------------------------------------------------------
   PurchasedProductsIndexAction.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Html;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Modules\Bootstrap\IncludeBootstrap;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\PaginationMeta;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductReadService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class PurchasedProductsIndexAction
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Html
 * @codeCoverageIgnore
 */
class PurchasedProductsIndexAction extends VuePageAction
{
    
    use IncludeBootstrap;
    
    /**
     * @param SoldProductReadService $service
     */
    public function __construct(private SoldProductReadService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $title    = $this->translate('HEADING_TITLE', 'stats_products_purchased');
        $template = dirname(__DIR__, 3) . '/ui/sold_products.html';
        
        $page   = (int)$request->getQueryParam('page', 1);
        $limit  = (int)$request->getQueryParam('limit', 20);
        $offset = $limit * ($page - 1);
        $data   = [
            'sold_products' => $this->service->getSoldProducts($limit, $offset)->toArray(),
            'pagination' => $this->getPaginationInfo($page, $limit),
        ];
        
        $this->includeBootstrapAssets($data);
        
        return $response->write($this->render($title, $template, $data));
    }
    
    
    /**
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    protected function getPaginationInfo(int $page, int $limit): array
    {
        $totalItems = $this->service->getSoldProductsTotalCount();
        $pagination = new PaginationMeta($page, $limit, $totalItems);
        
        return $pagination->toArray();
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return 'statistics__sold_product';
    }
}