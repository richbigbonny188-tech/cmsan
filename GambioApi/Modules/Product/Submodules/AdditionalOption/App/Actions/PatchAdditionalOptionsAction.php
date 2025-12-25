<?php
/*--------------------------------------------------------------
   PatchAdditionalOptionsAction.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions;

use Exception;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionReadService;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionWriteService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use InvalidArgumentException;

/**
 * Class PatchAdditionalOptionsAction
 *
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions
 */
class PatchAdditionalOptionsAction
{
    /**
     * @param AdditionalOptionReadService  $readService
     * @param AdditionalOptionWriteService $writeService
     * @param AdditionalOptionFactory      $optionFactory
     */
    public function __construct(
        private AdditionalOptionReadService  $readService,
        private AdditionalOptionWriteService $writeService,
        private AdditionalOptionFactory      $optionFactory
    ) {
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (($productId = (int)$request->getAttribute('productId')) === 0) {
            return $response->withStatus(400)->withJson(['errors' => ['Product ID can\'t be 0']]);
        }
        
        $additionalOptions = [];
        
        try {
            foreach ($request->getParsedBody() as $documentData) {
                $additionalOptions[] = $additionalOption = $this->readService->getAdditionalOptionById((int)($documentData['id']
                                                                                                             ?? 0));
                
                if ($additionalOption->productId() !== $productId) {
                    throw new InvalidArgumentException(sprintf('Additional option with ID "%s" belongs to the product with the ID "%s"',
                                                               $additionalOption->id(),
                                                               $additionalOption->productId()));
                }
                
                $this->patchAdditionalOption($documentData, $additionalOption);
            }
            
            $this->writeService->storeAdditionalOptions(...$additionalOptions);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
    
    
    /**
     * @param array            $documentData
     * @param AdditionalOption $option
     *
     * @return void
     */
    private function patchAdditionalOption(array $documentData, AdditionalOption $option): void
    {
        if (array_key_exists('imageListId', $documentData)) {
            $this->changeImageListId($documentData, $option);
        }
        
        if (array_key_exists('modelNumber', $documentData) || array_key_exists('weight', $documentData)
            || array_key_exists('price', $documentData)) {
            $this->changeOptionValueCustomization($documentData, $option);
        }
        
        if (array_key_exists('stockType', $documentData) || array_key_exists('stock', $documentData)) {
            $this->changeAdditionalOptionStock($documentData, $option);
        }
        
        if (array_key_exists('sortOrder', $documentData)) {
            $this->changeSortOrder($documentData, $option);
        }
    }
    
    
    /**
     * @param array            $documentData
     * @param AdditionalOption $option
     *
     * @return void
     */
    private function changeImageListId(array $documentData, AdditionalOption $option): void
    {
        if ($documentData['imageListId'] !== null) {
            $documentData['imageListId'] = (int)$documentData['imageListId'];
        } else {
            $documentData['imageListId'] = null;
        }
        
        $newImageListId = $this->optionFactory->createImageListId($documentData['imageListId']);
        
        $option->changeImageListId($newImageListId);
    }
    
    
    /**
     * @param array            $documentData
     * @param AdditionalOption $option
     *
     * @return void
     */
    private function changeOptionValueCustomization(array $documentData, AdditionalOption $option): void
    {
        $documentData['modelNumber'] = $documentData['modelNumber'] ?? $option->modelNumber();
        $documentData['weight']      = $documentData['weight'] ?? $option->weight();
        $documentData['price']       = $documentData['price'] ?? $option->price();
        
        $newOptionValueCustomization = $this->optionFactory->createOptionValueCustomization($documentData['modelNumber'],
                                                                                            (float)$documentData['weight'],
                                                                                            (float)$documentData['price']);
        
        $option->changeOptionValueCustomization($newOptionValueCustomization);
    }
    
    
    /**
     * @param array            $documentData
     * @param AdditionalOption $option
     *
     * @return void
     */
    private function changeAdditionalOptionStock(array $documentData, AdditionalOption $option): void
    {
        $documentData['stock']     = $documentData['stock'] ?? $option->stock();
        $documentData['stockType'] = $documentData['stockType'] ?? $option->stockType();
        
        $newProductVariantStock = $this->optionFactory->createAdditionalOptionStock((float)$documentData['stock'],
                                                                                    $documentData['stockType']);
        
        $option->changeAdditionalOptionStock($newProductVariantStock);
    }
    
    
    /**
     * @param array            $documentData
     * @param AdditionalOption $option
     *
     * @return void
     */
    private function changeSortOrder(array $documentData, AdditionalOption $option): void
    {
        $option->changeSortOrder((int)$documentData['sortOrder']);
    }
}