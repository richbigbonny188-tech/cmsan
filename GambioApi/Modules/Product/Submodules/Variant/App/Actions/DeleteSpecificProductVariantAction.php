<?php
/*--------------------------------------------------------------
   DeleteSpecificProductVariantAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Exception;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService as ProductVariantsWriteServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteSpecificProductVariantAction
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class DeleteSpecificProductVariantAction
{
    /**
     * @var ProductVariantsWriteServiceInterface
     */
    private $writeService;
    
    /**
     * @var ProductVariantsReadServiceInterface
     */
    private $readService;
    
    
    /**
     * DeleteSpecificProductVariantAction constructor.
     *
     * @param ProductVariantsWriteServiceInterface $writeService
     * @param ProductVariantsReadServiceInterface  $readService
     */
    public function __construct(
        ProductVariantsWriteServiceInterface $writeService,
        ProductVariantsReadServiceInterface $readService
    ) {
        $this->writeService = $writeService;
        $this->readService = $readService;
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
        $variantIds = explode(',', $request->getAttribute('variantIds'));
        $variantIds = array_map('intval', $variantIds);
        $productId  = (int)$request->getAttribute('productId');
    
        try {
    
            foreach ($variantIds as $variantId) {
                
                $variant = $this->readService->getProductVariantById($variantId);
                
                if ($variant->productId() !== $productId) {
    
                    $errorMessage = 'Product variant id "%s" belongs to product id "%s" and not "%s"';
                    $errorMessage = sprintf($errorMessage, $variantId, $variant->productId(), $productId);
    
                    throw new Exception($errorMessage);
                }
            }
            
            $this->writeService->deleteProductVariants(...$variantIds);
    
            return $response->withStatus(204);
        } catch (Exception $exception) {
    
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}