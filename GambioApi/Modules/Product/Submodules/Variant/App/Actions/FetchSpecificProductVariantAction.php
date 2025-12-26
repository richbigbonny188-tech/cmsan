<?php
/*--------------------------------------------------------------
   FetchSpecificProductVariantAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantDoesNotExist;
use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class FetchSpecificProductVariantAction
 *
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class FetchSpecificProductVariantAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var ProductVariantsReadServiceInterface
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * FetchSpecificProductVariantAction constructor.
     *
     * @param ProductVariantsReadServiceInterface $service
     * @param Url                                 $url
     */
    public function __construct(ProductVariantsReadServiceInterface $service, Url $url)
    {
        $this->service = $service;
        $this->url     = $url;
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
        try {
            $productId      = (int)$request->getAttribute('productId');
            $variantId      = (int)$request->getAttribute('variantId');
            $productVariant = $this->service->getProductVariantById($variantId);
            
            if ($productVariant->productId() !== $productId) {
                return $response->withStatus(404);
            }
            
            $data     = $productVariant->toArray();
            $links    = [
                'imageList' => $this->url->restApiV3() . '/image-lists/' . $data['imageListId'],
            ];
            $metaData = $this->createApiMetaData($links);
            
            return $response->withJson([
                                           'data'  => $data,
                                           '_meta' => $metaData,
                                       ]);
        } catch (ProductVariantDoesNotExist $exception) {
            
            return $response->withStatus(404);
        }
    }
}