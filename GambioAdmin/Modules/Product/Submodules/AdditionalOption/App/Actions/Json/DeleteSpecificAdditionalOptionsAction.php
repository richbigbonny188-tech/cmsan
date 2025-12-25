<?php
/*--------------------------------------------------------------
   DeleteSpecificAdditionalOptionsAction.php 2023-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService as AdditionalOptionWriteServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteSpecificAdditionalOptionsAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json
 * @codeCoverageIgnore
 */
class DeleteSpecificAdditionalOptionsAction extends AbstractAction
{
    /**
     * DeleteSpecificAdditionalOptionsAction constructor.
     *
     * @param AdditionalOptionWriteServiceInterface $writeService
     * @param AdditionalOptionReadServiceInterface  $readService
     */
    public function __construct(
        private AdditionalOptionWriteServiceInterface $writeService,
        private AdditionalOptionReadServiceInterface  $readService
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $additionalOptionIds = explode(',', $request->getAttribute('optionIds'));
        $additionalOptionIds = array_map('intval', $additionalOptionIds);
        $productId           = (int)$request->getAttribute('productId');
        
        try {
            foreach ($additionalOptionIds as $additionalOptionId) {
                $additionalOption = $this->readService->getAdditionalOptionById($additionalOptionId);
                
                if ($additionalOption->productId() !== $productId) {
                    $errorMessage = 'Product option id "%s" belongs to product id "%s" and not "%s"';
                    $errorMessage = sprintf($errorMessage,
                                            $additionalOptionId,
                                            $additionalOption->productId(),
                                            $productId);
                    
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