<?php
/*--------------------------------------------------------------
   UpdateAdditionalOptionsAction.php 2023-06-21
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
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use InvalidArgumentException;

/**
 * Class UpdateAdditionalOptionsAction
 *
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions
 */
class UpdateAdditionalOptionsAction
{
    /**
     * UpdateAdditionalOptionsAction constructor.
     *
     * @param AdditionalOptionApiRequestValidator $validator
     * @param AdditionalOptionFactory             $factory
     * @param AdditionalOptionReadService         $readService
     * @param AdditionalOptionWriteService        $writeService
     */
    public function __construct(
        private AdditionalOptionApiRequestValidator $validator,
        private AdditionalOptionFactory             $factory,
        private AdditionalOptionReadService         $readService,
        private AdditionalOptionWriteService $writeService
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
            return $response->withStatus(400)->withJson(['errors' => ['product id can\'t be 0']]);
        }
        
        $errors = $this->validator->validateUpdateBody($request->getParsedBody());
        
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $options = [];
        
        try {
            foreach ($request->getParsedBody() as $documentData) {
                $options[] = $option = $this->readService->getAdditionalOptionById((int)$documentData['id']);
                
                if ($option->productId() !== $productId) {
                    throw new InvalidArgumentException(sprintf('Additional option with id "%s" belongs to the product with the id "%s"',
                                                               $option->id(),
                                                               $option->productId()));
                }
                
                $customization = $this->factory->createOptionValueCustomization($documentData['modelNumber'],
                                                                                $documentData['weight'],
                                                                                $documentData['price']);
                $stock         = $this->factory->createAdditionalOptionStock($documentData['stock'],
                                                                             $documentData['stockType']);
                
                $option->changeImageListId($this->factory->createImageListId($documentData['imageListId']));
                $option->changeSortOrder($documentData['sortOrder']);
                $option->changeAdditionalOptionStock($stock);
                $option->changeOptionValueCustomization($customization);
            }
            
            $this->writeService->storeAdditionalOptions(...$options);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}