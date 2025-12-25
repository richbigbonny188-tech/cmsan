<?php
/*--------------------------------------------------------------
   CreateProductVariantsAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantCombinationAlreadyExists;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService as ProductVariantsWriteServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestParser;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateProductVariantsAction
 *
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class CreateProductVariantsAction
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
     * @var ProductVariantFactory
     */
    private $factory;
    
    /**
     * @var ProductVariantsWriteServiceInterface
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * CreateProductVariantsAction constructor.
     *
     * @param ProductVariantApiRequestValidator    $validator
     * @param ProductVariantApiRequestParser       $parser
     * @param ProductVariantFactory                $factory
     * @param ProductVariantsWriteServiceInterface $service
     * @param Url                                  $url
     */
    public function __construct(
        ProductVariantApiRequestValidator    $validator,
        ProductVariantApiRequestParser       $parser,
        ProductVariantFactory                $factory,
        ProductVariantsWriteServiceInterface $service,
        Url                                  $url
    ) {
        $this->validator = $validator;
        $this->parser    = $parser;
        $this->factory   = $factory;
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
        
        $errors = $this->validator->validateCreationBody($request->getParsedBody());
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->parser->parseProductVariantsData($request, $errors);
        if (empty($errors) === false || empty($creationArguments) === true) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $variantIds = $this->service->createMultipleProductVariants(...$creationArguments);
        } catch (InsertionOfProductVariantsFailed|ProductVariantCombinationAlreadyExists $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
        
        $links = [];
        foreach ($variantIds as $id) {
            $links[] = $this->url->restApiV3() . '/products/' . $productId . '/variants/' . $id->value();
        }
        
        return $response->withStatus(200)->withJson([
                                                        'data'  => $variantIds->toArray(),
                                                        '_meta' => $this->createApiMetaData($links),
                                                    ]);
    }
}