<?php
/*--------------------------------------------------------------
   PatchProductVariantsAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService as ProductVariantsWriteServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use InvalidArgumentException;

/**
 * Class PatchProductVariantsAction
 *
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class PatchProductVariantsAction
{
    /**
     * @var ProductVariantsReadServiceInterface
     */
    private $readService;
    
    /**
     * @var ProductVariantsWriteServiceInterface
     */
    private $writeService;
    
    /**
     * @var ProductVariantFactory
     */
    private $variantFactory;
    
    
    /**
     * @param ProductVariantsReadServiceInterface  $readService
     * @param ProductVariantsWriteServiceInterface $writeService
     * @param ProductVariantFactory                $variantFactory
     */
    public function __construct(
        ProductVariantsReadServiceInterface  $readService,
        ProductVariantsWriteServiceInterface $writeService,
        ProductVariantFactory                $variantFactory
    ) {
        $this->readService    = $readService;
        $this->writeService   = $writeService;
        $this->variantFactory = $variantFactory;
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
        
        $variants = [];
        
        try {
            foreach ($request->getParsedBody() as $documentData) {
                $variants[] = $variant = $this->readService->getProductVariantById((int)($documentData['id'] ?? 0));
                
                if ($variant->productId() !== $productId) {
                    throw new InvalidArgumentException(sprintf('Variant with ID "%s" belongs to the product with the ID "%s"',
                                                               $variant->id(),
                                                               $variant->productId()));
                }
                
                $this->patchProductVariant($documentData, $variant);
            }
            
            $this->writeService->storeProductVariants(...$variants);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
    
    
    /**
     * @param array          $documentData
     * @param ProductVariant $variant
     *
     * @return void
     */
    private function patchProductVariant(array $documentData, ProductVariant $variant): void
    {
        if (array_key_exists('combination', $documentData)) {
            $this->changeCombination($documentData, $variant);
        }
        
        if (array_key_exists('sortOrder', $documentData)) {
            $this->changeSortOrder($documentData, $variant);
        }
        
        if (array_key_exists('stockType', $documentData) || array_key_exists('stock', $documentData)) {
            $this->changeStock($documentData, $variant);
        }
        
        if (array_key_exists('imageListId', $documentData)) {
            $this->changeImageListId($documentData, $variant);
        }
        
        if (array_key_exists('deliveryTimeId', $documentData) || array_key_exists('priceType', $documentData)
            || array_key_exists('price', $documentData)
            || array_key_exists('weightType', $documentData)
            || array_key_exists('weight', $documentData)
            || array_key_exists('vpeScalarValue', $documentData)
            || array_key_exists('vpeUnitId', $documentData)) {
            $this->changeProductCustomization($documentData, $variant);
        }
        
        if (array_key_exists('modelNumber', $documentData) || array_key_exists('GTIN', $documentData)
            || array_key_exists('ASIN', $documentData)
            || array_key_exists('EAN', $documentData)) {
            $this->changeProductIdentificationNumbers($documentData, $variant);
        }
    }
    
    
    /**
     * @param array          $documentData
     * @param ProductVariant $variant
     *
     * @return void
     */
    private function changeCombination(array $documentData, ProductVariant $variant): void
    {
        $mappingCallback = function (array $optionAndOptionValueId): OptionAndOptionValueId {
            return $this->variantFactory->createOptionAndOptionValueId($optionAndOptionValueId['optionId'],
                                                                       $optionAndOptionValueId['optionValueId']);
        };
        
        $newCombination = $this->variantFactory->createOptionAndOptionValueIds(...
            array_map($mappingCallback, $documentData['combination']));
        
        $variant->changeCombination($newCombination);
    }
    
    
    /**
     * @param array          $documentData
     * @param ProductVariant $variant
     *
     * @return void
     */
    private function changeSortOrder(array $documentData, ProductVariant $variant): void
    {
        $variant->changeSortOrder((int)$documentData['sortOrder']);
    }
    
    
    /**
     * @param array          $documentData
     * @param ProductVariant $variant
     *
     * @return void
     */
    private function changeStock(array $documentData, ProductVariant $variant): void
    {
        $documentData['stock']     = $documentData['stock'] ?? $variant->stock();
        $documentData['stockType'] = $documentData['stockType'] ?? $variant->stockType();
        
        $newProductVariantStock = $this->variantFactory->createProductVariantStock((float)$documentData['stock'],
                                                                                   $documentData['stockType']);
        
        $variant->changeStock($newProductVariantStock);
    }
    
    
    /**
     * @param array          $documentData
     * @param ProductVariant $variant
     *
     * @return void
     */
    private function changeImageListId(array $documentData, ProductVariant $variant): void
    {
        if ($documentData['imageListId'] !== null) {
            $documentData['imageListId'] = (int)$documentData['imageListId'];
        } else {
            $documentData['imageListId'] = null;
        }
        
        $newImageListId = $this->variantFactory->createImageListId($documentData['imageListId']);
        
        $variant->changeImageListId($newImageListId);
    }
    
    
    /**
     * @param array          $documentData
     * @param ProductVariant $variant
     *
     * @return void
     */
    private function changeProductCustomization(array $documentData, ProductVariant $variant): void
    {
        $documentData['deliveryTimeId'] = $documentData['deliveryTimeId'] ?? $variant->deliveryTimeId();
        $documentData['priceType']      = $documentData['priceType'] ?? $variant->priceType();
        $documentData['price']          = $documentData['price'] ?? $variant->price();
        $documentData['weightType']     = $documentData['weightType'] ?? $variant->weightType();
        $documentData['weight']         = $documentData['weight'] ?? $variant->weight();
        $documentData['vpeScalarValue'] = $documentData['vpeScalarValue'] ?? $variant->vpeScalarValue();
        
        if (array_key_exists('vpeUnitId', $documentData) === false) {
            $documentData['vpeUnitId'] = $variant->vpeUnitId();
        } elseif ($documentData['vpeUnitId'] !== null) {
            $documentData['vpeUnitId'] = (int)$documentData['vpeUnitId'];
        }
        
        $newProductCustomization = $this->variantFactory->createProductCustomization((int)$documentData['deliveryTimeId'],
                                                                                     $documentData['priceType'],
                                                                                     (float)$documentData['price'],
                                                                                     $documentData['weightType'],
                                                                                     (float)$documentData['weight'],
                                                                                     (float)$documentData['vpeScalarValue'],
                                                                                     $documentData['vpeUnitId']);
        
        $variant->changeProductCustomization($newProductCustomization);
    }
    
    
    /**
     * @param array          $documentData
     * @param ProductVariant $variant
     *
     * @return void
     */
    private function changeProductIdentificationNumbers(array $documentData, ProductVariant $variant): void
    {
        $documentData['modelNumber'] = $documentData['modelNumber'] ?? $variant->modelNumber();
        $documentData['GTIN']        = $documentData['GTIN'] ?? $variant->gtin();
        $documentData['ASIN']        = $documentData['ASIN'] ?? $variant->asin();
        $documentData['EAN']         = $documentData['EAN'] ?? $variant->ean();
        
        $newProductIdentificationNumbers = $this->variantFactory->createProductIdentificationNumbers($documentData['modelNumber'],
                                                                                                     $documentData['EAN'],
                                                                                                     $documentData['GTIN'],
                                                                                                     $documentData['ASIN']);
        
        $variant->changeProductIdentificationNumbers($newProductIdentificationNumbers);
    }
}