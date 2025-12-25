<?php
/*--------------------------------------------------------------
   GenerateProductVariantsAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsGenerationService as ProductVariantsGenerationServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestParser;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class GenerateProductVariantsAction
 *
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class GenerateProductVariantsAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var ProductVariantApiRequestValidator
     */
    private $validator;
    
    /**
     * @var ProductVariantApiRequestParser
     */
    private $parser;
    
    /**
     * @var ProductVariantsGenerationServiceInterface
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * GenerateProductVariantsAction constructor.
     *
     * @param ProductVariantApiRequestValidator         $validator
     * @param ProductVariantApiRequestParser            $parser
     * @param ProductVariantsGenerationServiceInterface $service
     * @param Url                                       $url
     */
    public function __construct(
        ProductVariantApiRequestValidator         $validator,
        ProductVariantApiRequestParser            $parser,
        ProductVariantsGenerationServiceInterface $service,
        Url                                       $url
    ) {
        $this->validator = $validator;
        $this->parser    = $parser;
        $this->service   = $service;
        $this->url       = $url;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     *
     * @todo validate option value belongs to option
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (($productId = (int)$request->getAttribute('productId')) === 0) {
            return $response->withStatus(400)->withJson(['errors' => ['product id can\'t be 0']]);
        }
        
        $optionAndOptionValueIds = $request->getParsedBody();
        $errors                  = $this->validator->validateGenerationBody($optionAndOptionValueIds);
        
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $limit  = $this->parser->getLimit($request);
        $offset = $this->parser->getOffset($request);
        
        try {
            $variantIds = $this->service->generateProductVariants($productId,
                                                                  $optionAndOptionValueIds,
                                                                  $limit,
                                                                  $offset);
        } catch (InsertionOfProductVariantsFailed $insertionOfProductVariantFailed) {
            return $response->withStatus(422)->withJson(['errors' => [$insertionOfProductVariantFailed->getMessage()]]);
        }
        
        $links = [];
        foreach ($variantIds as $variantId) {
            $links[] = $this->url->restApiV3() . '/products/' . $productId . '/variants/' . $variantId->value();
        }
        $metaData = $this->createApiMetaData($links);
        
        return $response->withStatus(200)->withJson(['data' => $variantIds->toArray(), '_meta' => $metaData]);
    }
}