<?php
/*--------------------------------------------------------------------------------------------------
    FetchAvailableProductDownloadsPerIdAction.php 2023-06-21
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
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAvailableProductDownloadsPerIdAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json
 */
class FetchSpecificAvailableOptionsAction extends AbstractAction
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
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $productId = (int)$request->getAttribute('productId');
        $optionId  = (int)$request->getAttribute('optionId');
        
        try {
            $option         = $this->optionReadService->getOptionById($optionId);
            $productOptions = $this->productDownloadReadService->getProductDownloadsByProductId($productId);
            $variants       = $this->productVariantsReadService->getProductVariantsByProductId($productId);
            $valuesCreated  = array_column($productOptions->toArray(), 'optionValueId');
            $json           = [];
            
            foreach ($variants as $variant) {
                foreach ($variant->combination() as $optionAndOptionValueId) {
                    $valuesCreated[] = $optionAndOptionValueId->optionValueId();
                    
                    if ($optionAndOptionValueId->optionId() === $optionId) {
                        return $response->withJson(['data' => []]);
                    }
                }
            }
            
            $valuesCreated = array_unique($valuesCreated);
            
            foreach ($option->values() as $value) {
                if (in_array($value->id(), $valuesCreated, true)) {
                    continue;
                }
                
                $json[$value->id()] = [
                    'id'        => $value->id(),
                    'sortOrder' => $value->sortOrder(),
                    'details'   => $value->toArray()['details'],
                ];
            }
            
            if (count($json)) {
                $callback = fn(array $a, array $b): int => $a['sortOrder'] <=> $b['sortOrder'];
                usort($json, $callback);
            }
            
            return $response->withJson(['data' => array_values($json)]);
        } catch (OptionDoesNotExistException) {
            return $response->withStatus(404);
        } catch (Exception $exception) {
            return $response->withStatus(409)->withJson(['message' => $exception->getMessage()]);
        }
    }
}