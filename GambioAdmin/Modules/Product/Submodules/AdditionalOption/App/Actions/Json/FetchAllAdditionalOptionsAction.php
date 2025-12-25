<?php
/*--------------------------------------------------------------
   FetchAllAdditionalOptionsAction.php 2023-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllAdditionalOptionsAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json
 * @todo OptionReadService and ProductPriceConversionService in use here
 */
class FetchAllAdditionalOptionsAction extends AbstractAdditionalOptionAction
{
    /**
     * @param AdditionalOptionReadServiceInterface $productOptionReadService
     * @param OptionReadServiceInterface           $optionReadService
     * @param OptionFactory                        $optionFactory
     * @param Connection                           $connection
     * @param ProductPriceConversionService        $priceModifyService
     */
    public function __construct(
        private AdditionalOptionReadServiceInterface $productOptionReadService,
        private OptionReadServiceInterface           $optionReadService,
        private OptionFactory                        $optionFactory,
        Connection                                   $connection,
        private ProductPriceConversionService        $priceModifyService
    ) {
        $this->setConnection($connection);
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $json                     = [];
        $productId                = (int)$request->getAttribute('productId');
        $productOptions           = $this->productOptionReadService->getAdditionalOptionsByProductId($productId);
        $downloadProductOptionIds = $this->downloadProductOptionIds();
        
        foreach ($productOptions as $productOption) {
            if (in_array($productOption->id(), $downloadProductOptionIds, true)) {
                continue;
            }
            
            $data = $productOption->toArray();
            unset($data['optionId'], $data['optionValueId'], $data['productId']);
            
            $optionValueId       = $this->optionFactory->createOptionValueId($productOption->optionValueId());
            $data['optionValue'] = $this->optionReadService->getOptionById($productOption->optionId())
                ->values()
                ->getById($optionValueId)
                ->toArray();
            
            $data['price'] = $this->priceModifyService->getGrossPrice($data['price'], $productId);
            
            $json[$productOption->optionId()]['values'][] = $data;
        }
        
        foreach (array_keys($json) as $optionId) {
            $option                     = $this->optionReadService->getOptionById($optionId);
            $optionId                   = $option->id();
            $json[$optionId]['id']      = $optionId;
            $json[$optionId]['details'] = $option->toArray()['details'];
            
            ksort($json[$optionId]);
            $callback = static fn(array $a, array $b): int => (int)$a['sortOrder'] <=> (int)$b['sortOrder'];
            usort($json[$optionId]['values'], $callback);
        }
        
        return $response->withJson(['data' => array_values($json)]);
    }
}