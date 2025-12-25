<?php
/*--------------------------------------------------------------
   DeleteAllProductVariantsAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteAllProductVariantsAction
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class DeleteAllProductVariantsAction
{
    /**
     * @var ProductVariantsWriteService
     */
    private $service;
    
    
    /**
     * DeleteAllProductVariantsAction constructor.
     *
     * @param ProductVariantsWriteService $service
     */
    public function __construct(ProductVariantsWriteService $service)
    {
        $this->service = $service;
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
        if (($productId = (int)$request->getAttribute('productId')) !== 0) {
            
            $this->service->deleteAllProductVariantsByProductId($productId);
        }
    
        return $response->withStatus(204);
    }
}