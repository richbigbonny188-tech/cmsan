<?php
/*--------------------------------------------------------------
   RemoveOptionAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsGenerationService as ProductVariantsGenerationServiceInterface;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class RemoveOptionAction
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class RemoveOptionAction
{
    /**
     * @var ProductVariantApiRequestValidator
     */
    private $validator;
    
    /**
     * @var ProductVariantsGenerationServiceInterface
     */
    private $service;
    
    
    /**
     * RemoveOptionAction constructor.
     *
     * @param ProductVariantApiRequestValidator         $validator
     * @param ProductVariantsGenerationServiceInterface $service
     */
    public function __construct(
        ProductVariantApiRequestValidator $validator,
        ProductVariantsGenerationServiceInterface $service
    ){
        $this->validator = $validator;
        $this->service   = $service;
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
    
        $parsedBody = $request->getParsedBody();
        $errors     = $this->validator->validateOptionRemovalBody($parsedBody);
    
        if (empty($errors) === false) {
        
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
    
            foreach ($parsedBody as ['optionId' => $optionId, 'retainableOptionValueId' => $retainableOptionValueId]) {
                
                $this->service->removeOptionFromExistingProductVariants($productId, $optionId, $retainableOptionValueId);
            }
    
            return $response->withStatus(204);
            
        } catch (Exception $exception) {
    
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}