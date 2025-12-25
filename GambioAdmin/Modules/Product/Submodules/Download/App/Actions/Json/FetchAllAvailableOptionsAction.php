<?php
/*--------------------------------------------------------------------------------------------------
    FetchAllAvailableOptionsAction.php 2023-06-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllAvailableOptionsAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json
 */
class FetchAllAvailableOptionsAction extends AbstractAction
{
    /**
     * @param OptionReadServiceInterface          $optionReadService
     * @param ProductDownloadReadServiceInterface $productDownloadReadService
     * @param ProductVariantsReadServiceInterface $productVariantsReadService
     */
    public function __construct(
        private OptionReadServiceInterface          $optionReadService,
        private ProductDownloadReadServiceInterface $productDownloadReadService,
        private ProductVariantsReadServiceInterface $productVariantsReadService
    ) {
    }
    
    
    /**
     * @inheritcDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $productId = (int)$request->getAttribute('productId');
        
        try {
            $options          = $this->optionReadService->getAllOptions();
            $productOptions   = $this->productDownloadReadService->getProductDownloadsByProductId($productId);
            $variants         = $this->productVariantsReadService->getProductVariantsByProductId($productId);
            $valuesCreated    = array_column($productOptions->toArray(), 'optionValueId');
            $variantOptionIds = $json = [];
            
            foreach ($variants as $variant) {
                foreach ($variant->combination() as $optionAndOptionValueId) {
                    $variantOptionIds[] = $optionAndOptionValueId->optionId();
                }
            }
            
            $variantOptionIds = array_unique($variantOptionIds);
            
            foreach ($options as $option) {
                if (in_array($option->id(), $variantOptionIds, true)) {
                    continue;
                }
                
                foreach ($option->values() as $value) {
                    if (in_array($value->id(), $valuesCreated, true)) {
                        continue;
                    }
                    
                    if (isset($json[$option->id()]) === false) {
                        $json[$option->id()]['id']      = $option->id();
                        $json[$option->id()]['details'] = $option->toArray()['details'];
                    }
                    
                    $json[$option->id()]['values'][] = [
                        'id'        => $value->id(),
                        'sortOrder' => $value->sortOrder(),
                        'details'   => $value->toArray()['details'],
                    ];
                }
                
                if (isset($json[$option->id()]['values']) && count($json[$option->id()]['values'])) {
                    $callback = fn(array $a, array $b): int => $a['sortOrder'] <=> $b['sortOrder'];
                    usort($json[$option->id()]['values'], $callback);
                }
            }
            
            return $response->withJson(['data' => array_values($json)]);
        } catch (Exception $exception) {
            return $response->withStatus(409)->withJson(['error' => $exception->getMessage()]);
        }
    }
}