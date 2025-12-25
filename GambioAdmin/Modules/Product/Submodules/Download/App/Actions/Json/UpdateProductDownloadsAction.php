<?php
/*--------------------------------------------------------------
   UpdateProductDownloadsAction.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService;
use Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadRequestParser;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFactory;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadReadService as ProductDownloadReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadWriteService as ProductDownloadWriteServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use InvalidArgumentException;

/**
 * Class UpdateProductDownloadsAction
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json
 */
class UpdateProductDownloadsAction extends AbstractProductDownloadAction
{
    /**
     * UpdateProductOptionsAction constructor.
     *
     * @param ProductDownloadFactory               $factory
     * @param ProductDownloadReadServiceInterface  $readService
     * @param ProductDownloadWriteServiceInterface $writeService
     * @param Connection                           $connection
     * @param ProductPriceConversionService        $priceModifyService
     * @param ProductDownloadRequestParser         $requestParser
     */
    public function __construct(
        private ProductDownloadFactory               $factory,
        private ProductDownloadReadServiceInterface  $readService,
        private ProductDownloadWriteServiceInterface $writeService,
        Connection                                   $connection,
        private ProductPriceConversionService        $priceModifyService,
        private ProductDownloadRequestParser         $requestParser
    ) {
        $this->setConnection($connection);
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
                $options[] = $option = $this->readService->getProductDownloadById((int)$documentData['id']);
                
                if ($option->productId() !== $productId) {
                    throw new InvalidArgumentException(sprintf('Product option with id "%s" belongs to the product with the id "%s"',
                                                                $option->id(),
                                                                $option->productId()));
                }
                
                $documentData['price'] = $this->priceModifyService->getNetPrice($documentData['price'], $productId);
                
                $customization = $this->requestParser->parseOptionValueCustomizationData($documentData);
                $stock         = $this->factory->createProductDownloadStock($documentData['stock'],
                                                                            $documentData['stockType']);
                
                $option->changeImageListId($this->factory->createImageListId($documentData['imageListId']));
                $option->changeSortOrder($documentData['sortOrder']);
                $option->changeProductOptionStock($stock);
                $option->changeOptionValueCustomization($customization);
                
                [
                    'filePath' => $filePath,
                    'maxCount' => $maxCount,
                    'maxDays'  => $maxDays,
                ] = $documentData;
                
                if (isset($filePath, $maxCount, $maxDays)) {
                    $this->assignDownloadData($option->id(), $filePath, $maxDays, $maxCount);
                }
            }
            
            $this->writeService->storeProductDownloads(...$options);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}