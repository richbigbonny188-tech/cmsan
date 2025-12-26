<?php
/*--------------------------------------------------------------
   FetchAllProductDownloadsAction.php 2023-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllProductDownloadsAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json
 */
class FetchAllProductDownloadsAction extends AbstractProductDownloadAction
{
    /**
     * @param ProductDownloadReadServiceInterface $productDownloadReadService
     * @param OptionReadServiceInterface          $optionReadService
     * @param OptionFactory                       $optionFactory
     * @param Connection                          $connection
     * @param ProductPriceConversionService       $priceModifyService
     */
    public function __construct(
        private ProductDownloadReadServiceInterface $productDownloadReadService,
        private OptionReadServiceInterface          $optionReadService,
        private OptionFactory                       $optionFactory,
        Connection                                  $connection,
        private ProductPriceConversionService       $priceModifyService
    ) {
        $this->setConnection($connection);
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $json           = [];
        $productId      = (int)$request->getAttribute('productId');
        $productOptions = $this->productDownloadReadService->getProductDownloadsByProductId($productId);
        
        foreach ($productOptions as $productOption) {
            if (in_array($productOption->id(), $this->downloadProductOptionIds(), true) === false) {
                continue;
            }
            
            $data = $productOption->toArray();
            $data = $this->addDownloadDataToArray($data);
            unset($data['optionId'], $data['optionValueId'], $data['productId']);
            
            $optionValueId       = $this->optionFactory->createOptionValueId($productOption->optionValueId());
            $data['optionValue'] = $this->optionReadService->getOptionById($productOption->optionId())
                ->values()
                ->getById($optionValueId)
                ->toArray();
            
            $data['price'] = $this->priceModifyService->getGrossPrice($data['price'], $productId, 2);
            
            $json[$productOption->optionId()]['values'][] = $data;
        }
        
        foreach (array_keys($json) as $optionId) {
            $option                         = $this->optionReadService->getOptionById($optionId);
            $json[$option->id()]['id']      = $option->id();
            $json[$option->id()]['details'] = $option->toArray()['details'];
            
            $callback = fn (array $a, array $b) => (int)$a['sortOrder'] <=> (int)$b['sortOrder'];
            
            ksort($json[$option->id()]);
            usort($json[$option->id()]['values'], $callback);
        }
        
        return $response->withJson(['data' => array_values($json)]);
    }
}