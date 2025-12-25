<?php
/*--------------------------------------------------------------
   UpdateAdditionalOptionsAction.php 2023-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionRequestParser;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService as AdditionalOptionWriteServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateAdditionalOptionsAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json
 */
class UpdateAdditionalOptionsAction extends AbstractAction
{
    /**
     * UpdateAdditionalOptionsAction constructor.
     *
     * @param AdditionalOptionFactory               $factory
     * @param AdditionalOptionReadServiceInterface  $readService
     * @param AdditionalOptionWriteServiceInterface $writeService
     * @param ProductPriceConversionService         $priceModifyService
     * @param AdditionalOptionRequestParser         $requestParser
     */
    public function __construct(
        private AdditionalOptionFactory               $factory,
        private AdditionalOptionReadServiceInterface  $readService,
        private AdditionalOptionWriteServiceInterface $writeService,
        private ProductPriceConversionService         $priceModifyService,
        private AdditionalOptionRequestParser         $requestParser
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        if (($productId = (int)$request->getAttribute('productId')) === 0) {
            return $response->withStatus(400)->withJson(['errors' => ['product id can\'t be 0']]);
        }
        
        $parsedBody = $request->getParsedBody();
        $options    = [];
        
        try {
            foreach ($parsedBody as $documentData) {
                $options[] = $option = $this->readService->getAdditionalOptionById((int)$documentData['id']);
                
                if ($option->productId() !== $productId) {
                    throw new \InvalidArgumentException(sprintf('Product option with id "%s" belongs to the product with the id "%s"',
                                                                $option->id(),
                                                                $option->productId()));
                }
                
                $documentData['price'] = $this->priceModifyService->getNetPrice($documentData['price'], $productId);
                
                $customization = $this->requestParser->parseOptionValueCustomizationData($documentData);
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