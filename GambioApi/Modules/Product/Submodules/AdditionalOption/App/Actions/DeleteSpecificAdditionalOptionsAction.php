<?php
/*--------------------------------------------------------------
   DeleteSpecificAdditionalOptionsAction.php 2023-06-21
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
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteSpecificAdditionalOptionsAction
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions
 */
class DeleteSpecificAdditionalOptionsAction
{
    /**
     * DeleteSpecificAdditionalOptionsAction constructor.
     *
     * @param AdditionalOptionWriteService $writeService
     * @param AdditionalOptionReadService  $readService
     */
    public function __construct(
        private AdditionalOptionWriteService $writeService,
        private AdditionalOptionReadService  $readService
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
        $additionalOptionIds = explode(',', $request->getAttribute('optionIds'));
        $additionalOptionIds = array_map('intval', $additionalOptionIds);
        $productId        = (int)$request->getAttribute('productId');
        
        try {
    
            foreach ($additionalOptionIds as $additionalOptionId) {
                
                $additionalOption = $this->readService->getAdditionalOptionById($additionalOptionId);
                
                if ($additionalOption->productId() !== $productId) {
    
                    $errorMessage = 'Additional option id "%s" belongs to product id "%s" and not "%s"';
                    $errorMessage = sprintf($errorMessage, $additionalOptionId, $additionalOption->productId(), $productId);
                    
                    throw new Exception($errorMessage);
                }
            }
            
            $this->writeService->deleteAdditionalOptions(...$additionalOptionIds);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}