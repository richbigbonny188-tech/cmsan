<?php
/*--------------------------------------------------------------
   CreateAdditionalOptionsAction.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions;

use Exception;
use Gambio\Admin\Modules\Product\Services\AdditionalOptionWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionApiRequestParser;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateAdditionalOptionsAction
 *
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions
 */
class CreateAdditionalOptionsAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * CreateAdditionalOptionsAction constructor.
     *
     * @param AdditionalOptionApiRequestValidator $validator
     * @param AdditionalOptionApiRequestParser    $parser
     * @param AdditionalOptionWriteService        $service
     * @param Url                                 $url
     */
    public function __construct(
        private AdditionalOptionApiRequestValidator $validator,
        private AdditionalOptionApiRequestParser    $parser,
        private AdditionalOptionWriteService        $service,
        private Url                                 $url
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
        
        $errors = $this->validator->validateCreationBody($request->getParsedBody());
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->parser->parseAdditionalOptionsData($request, $errors);
        if (empty($errors) === false || empty($creationArguments) === true) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $optionIds = $this->service->createMultipleAdditionalOptions(...$creationArguments);
            $links     = [];
            foreach ($optionIds as $id) {
                $links[] = $this->url->restApiV3() . '/products/' . $productId . '/options/' . $id->value();
            }
            
            return $response->withStatus(200)->withJson([
                                                            'data'  => $optionIds->toArray(),
                                                            '_meta' => $this->createApiMetaData($links),
                                                        ]);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}