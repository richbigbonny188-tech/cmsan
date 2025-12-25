<?php
/*--------------------------------------------------------------
   UpdateProductVariantsAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService as ProductVariantsWriteServiceInterface;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use InvalidArgumentException;

/**
 * Class UpdateProductVariantsAction
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class UpdateProductVariantsAction
{
    /**
     * @var ProductVariantApiRequestValidator
     */
    private $validator;
    
    /**
     * @var ProductVariantFactory
     */
    private $factory;
    
    /**
     * @var ProductVariantsReadServiceInterface
     */
    private $readService;
    
    /**
     * @var ProductVariantsWriteServiceInterface
     */
    private $writeService;
    
    
    /**
     * UpdateProductVariantsAction constructor.
     *
     * @param ProductVariantApiRequestValidator    $validator
     * @param ProductVariantFactory                $factory
     * @param ProductVariantsReadServiceInterface  $readService
     * @param ProductVariantsWriteServiceInterface $writeService
     */
    public function __construct(
        ProductVariantApiRequestValidator $validator,
        ProductVariantFactory $factory,
        ProductVariantsReadServiceInterface $readService,
        ProductVariantsWriteServiceInterface $writeService
    ) {
        $this->validator    = $validator;
        $this->factory      = $factory;
        $this->readService  = $readService;
        $this->writeService = $writeService;
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
            
            return $response->withStatus(400)->withJson(['errors' => ['product id can\'t be 0']]);
        }
        
        $errors = $this->validator->validateUpdateBody($request->getParsedBody());
        
        if (empty($errors) === false) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $variants = [];
        
        try {
            
            foreach ($request->getParsedBody() as $documentData) {
                
                $variants[] = $variant = $this->readService->getProductVariantById((int)$documentData['id']);
                
                if ($variant->productId() !== $productId) {
                    
                    throw new InvalidArgumentException(sprintf('Variant with id "%s" belongs to the product with the id "%s"', $variant->id(), $variant->productId()));
                }
                
                $variant->changeSortOrder((int)$documentData['sortOrder']);
                $variant->changeStock($this->factory->createProductVariantStock((int)$documentData['stock'], $documentData['stockType']));
                $variant->changeImageListId($this->factory->createImageListId($documentData['imageListId']));
                
                $productCustomization = $this->factory->createProductCustomization($documentData['deliveryTimeId'],
                                                                                   $documentData['priceType'],
                                                                                   $documentData['price'],
                                                                                   $documentData['weightType'],
                                                                                   $documentData['weight'],
                                                                                   $documentData['vpeScalarValue'],
                                                                                   $documentData['vpeUnitId']);
                $variant->changeProductCustomization($productCustomization);
                
                $productIdentificationNumbers = $this->factory->createProductIdentificationNumbers($documentData['modelNumber'] ?? '',
                                                                                                   $documentData['EAN'] ?? '',
                                                                                                   $documentData['GTIN'] ?? '',
                                                                                                   $documentData['ASIN'] ?? '');
                $variant->changeProductIdentificationNumbers($productIdentificationNumbers);
            }
            
            $this->writeService->storeProductVariants(...$variants);
    
            return $response->withStatus(204);
            
        } catch (Exception $exception) {
            
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
    
    
}