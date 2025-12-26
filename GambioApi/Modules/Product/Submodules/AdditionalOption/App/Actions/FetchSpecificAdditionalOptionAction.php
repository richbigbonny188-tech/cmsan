<?php
/*--------------------------------------------------------------
   FetchSpecificAdditionalOptionAction.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions;

use Gambio\Admin\Modules\Product\Services\AdditionalOptionReadService;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionDoesNotExistException;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class FetchSpecificAdditionalOptionAction
 *
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions
 */
class FetchSpecificAdditionalOptionAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * FetchSpecificAdditionalOptionAction constructor.
     *
     * @param AdditionalOptionReadService $service
     * @param Url                         $url
     */
    public function __construct(private AdditionalOptionReadService $service, private Url $url) { }
    
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
            $productId       = (int)$request->getAttribute('productId');
            $additionalOptionId = (int)$request->getAttribute('optionId');
            $additionalOption   = $this->service->getAdditionalOptionById($additionalOptionId);
            
            if ($productId !== $additionalOption->productId()) {
                $errorMessage = 'Product option id "%s" belongs to product id "%s" and not "%s"';
                $errorMessage = sprintf($errorMessage, $additionalOptionId, $additionalOption->productId(), $productId);
                
                return $response->withStatus(404)->withJson(['errors' => [[$errorMessage]]]);
            }
            
            $data     = $additionalOption->toArray();
            $links    = [
                'option'    => $this->url->restApiV3() . '/options/' . $data['optionId'],
                'imageList' => $this->url->restApiV3() . '/image-lists/' . $data['imageListId'],
            ];
            $metaData = $this->createApiMetaData($links);
            
            return $response->withJson([
                                           'data'  => $data,
                                           '_meta' => $metaData,
                                       ]);
        } catch (AdditionalOptionDoesNotExistException) {
            return $response->withStatus(404);
        }
    }
}