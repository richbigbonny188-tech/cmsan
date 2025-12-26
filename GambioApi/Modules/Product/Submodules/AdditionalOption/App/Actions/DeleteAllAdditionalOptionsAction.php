<?php
/*--------------------------------------------------------------
   DeleteAllProductOptionsAction.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions;

use Gambio\Admin\Modules\Product\Services\AdditionalOptionWriteService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\DeletionOfAdditionalOptionsFailedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteAllAdditionalOptionsAction
 *
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions
 */
class DeleteAllAdditionalOptionsAction
{
    /**
     * DeleteAllProductOptionsAction constructor.
     *
     * @param AdditionalOptionWriteService $service
     */
    public function __construct(private AdditionalOptionWriteService $service) { }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            
            if (($productId = (int)$request->getAttribute('productId')) !== 0) {
                
                $this->service->deleteAllAdditionalOptionsByProductId($productId);
            }
            
            return $response->withStatus(204);
        } catch (DeletionOfAdditionalOptionsFailedException|OperationHasNotBeenPermittedException $exception) {
            
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}