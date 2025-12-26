<?php
/* --------------------------------------------------------------
   ViewedProductsStatistic.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Actions;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Modules\Bootstrap\IncludeBootstrap;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\Pagination;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service\Exceptions\RetrieveViewedProductsFailedException;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service\ProductStatisticsService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;

/**
 * Class ViewedProductsStatistic
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Actions
 */
class ViewedProductsStatistic extends VuePageAction
{
    private const QUERY_PARAM_PAGE       = 'page';
    private const QUERY_PARAM_PER_PAGE   = 'per_page';
    private const DEFAULT_ITEMS_PER_PAGE = 20;
    private const DEFAULT_PAGE           = 1;
    
    private const LANGUAGE_SECTION  = 'stats_products_viewed';
    private const PHRASE_PAGE_TITLE = 'HEADING_TITLE';
    
    use IncludeBootstrap;
    
    public function __construct(private ProductStatisticsService $service, private AdminMenuService $menuService)
    {
        // Workaround to keep the Admin Menu opened
        $this->menuService->changeSelectedAdminPage('statistics/products/purchased');
    }
    
    
    /**
     * @inheritDoc
     * @throws RenderingFailedException|RetrieveViewedProductsFailedException
     */
    public function handle(Request $request, Response $response): Response
    {
        [$currentPage, $itemsPerPage] = $this->getPageInfo($request);
        
        $pagination     = new Pagination($currentPage, $itemsPerPage);
        $viewedProducts = $this->service->getProductViewStatistic($pagination);
        
        $data = $viewedProducts->toArray();
        $this->includeBootstrapAssets($data);
        
        $templatePath = __DIR__ . '/../../ui/viewed_products.html';
        
        $pageTitle = $this->translate(self::PHRASE_PAGE_TITLE, self::LANGUAGE_SECTION);
        $template  = $this->render($pageTitle, $templatePath, $data);
        
        return $response->write($template);
    }
    
    
    /**
     * Returns page information for pagination.
     *
     * @param Request $request
     *
     * @return array{0: int, 1: int}
     */
    private function getPageInfo(Request $request): array
    {
        $currentPage  = $request->getQueryParam(self::QUERY_PARAM_PAGE, self::DEFAULT_PAGE);
        $itemsPerPage = $request->getQueryParam(self::QUERY_PARAM_PER_PAGE, self::DEFAULT_ITEMS_PER_PAGE);
        
        return [(int)$currentPage, (int)$itemsPerPage];
    }
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return 'statistics__products_viewed';
    }
    
}