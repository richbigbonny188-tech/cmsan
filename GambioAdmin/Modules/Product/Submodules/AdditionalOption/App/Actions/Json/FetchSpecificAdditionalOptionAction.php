<?php
/*--------------------------------------------------------------
   FetchSpecificAdditionalOptionAction.php 2023-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionDoesNotExistException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;

/**
 * Class FetchSpecificAdditionalOptionAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json
 */
class FetchSpecificAdditionalOptionAction extends AbstractAdditionalOptionAction
{
    /**
     * FetchSpecificAdditionalOptionAction constructor.
     *
     * @param AdditionalOptionReadServiceInterface $productOptionReadService
     * @param OptionReadServiceInterface        $optionReadService
     * @param OptionFactory                     $optionFactory
     * @param Connection                        $connection
     * @param ProductPriceConversionService     $priceModifyService
     */
    public function __construct(
        private AdditionalOptionReadServiceInterface $productOptionReadService,
        private OptionReadServiceInterface $optionReadService,
        private OptionFactory $optionFactory,
        Connection $connection,
        private ProductPriceConversionService $priceModifyService
    ) {
        $this->setConnection($connection);
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
    
            $productId       = (int)$request->getAttribute('productId');
            $productOptionId = (int)$request->getAttribute('optionId');
            $productOption   = $this->productOptionReadService->getAdditionalOptionById($productOptionId);
            $json            = [];
            
            if (in_array($productOption->id(), $this->downloadProductOptionIds(), true)) {
                
                throw AdditionalOptionDoesNotExistException::forAdditionalOptionId($productOption->id());
            }
    
            if ($productId !== $productOption->productId()) {
        
                $errorMessage = 'Product option id "%s" belongs to product id "%s" and not "%s"';
                $errorMessage = sprintf($errorMessage, $productOptionId, $productOption->productId(), $productId);
        
                return $response->withStatus(404)->withJson(['errors' => [[$errorMessage]]]);
            }
    
            $data = $productOption->toArray();
            unset($data['optionId'], $data['optionValueId'], $data['productId']);
    
            $optionValueId       = $this->optionFactory->createOptionValueId($productOption->optionValueId());
            $data['optionValue'] = $this->optionReadService->getOptionById($productOption->optionId())
                ->values()
                ->getById($optionValueId)
                ->toArray();
    
            $data['price'] = $this->priceModifyService->getGrossPrice($data['price'], $productId);
            
            $json['values'][] = $data;
    
            $option          = $this->optionReadService->getOptionById($productOption->optionId());
            $json['id']      = $option->id();
            $json['details'] = $option->toArray()['details'];
    
            ksort($json);
            usort($json['values'], static function (array $a, array $b) {
                return (int)$a['sortOrder'] <=> (int)$b['sortOrder'];
            });
    
            return $response->withJson(['data' => $json,]);
            
        } catch (AdditionalOptionDoesNotExistException | OptionDoesNotExistException $exception) {
    
            return $response->withStatus(404);
        }
    }
}