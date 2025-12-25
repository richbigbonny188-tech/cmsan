<?php
/*--------------------------------------------------------------------
 AddOptionValuesAction.php 2023-06-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsGenerationService as ProductVariantsGenerationServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestParser;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class AddOptionValuesAction
 *
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class AddOptionValuesAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var ProductVariantApiRequestValidator
     */
    private $validator;
    
    /**
     * @var ProductVariantsGenerationServiceInterface
     */
    private $service;
    
    /**
     * @var ProductVariantApiRequestParser
     */
    private $parser;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * RemoveOptionAction constructor.
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
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (($productId = (int)$request->getAttribute('productId')) === 0) {
            return $response->withStatus(400)->withJson(['errors' => ['Product ID can\'t be 0']]);
        }
        
        $optionAndOptionValueIds = $request->getParsedBody();
        $errors                  = $this->validator->validateGenerationBody($optionAndOptionValueIds);
        
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $parsedData = $this->parser->parseAddOptionValuesFromDocumentData($optionAndOptionValueIds);
            $variantIds = [];
            $links      = [];
            
            foreach ($parsedData as $optionId => $optionValueIds) {
                $result = $this->service->addOptionToExistingProductVariants($productId, $optionId, $optionValueIds);
                
                foreach ($result as $variantId) {
                    $variantIds[] = $variantId->value();
                    $links[]      = $this->url->restApiV3() . '/products/' . $productId . '/variants/'
                                    . $variantId->value();
                }
            }
            
            $metaData = $this->createApiMetaData($links);
            
            return $response->withStatus(200)->withJson(['data' => $variantIds, '_meta' => $metaData]);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}