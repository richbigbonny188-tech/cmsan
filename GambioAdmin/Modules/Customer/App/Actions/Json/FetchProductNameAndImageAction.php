<?php
/*--------------------------------------------------------------
   FetchProductNameAndImageAction.php 2022-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Gambio\Admin\Modules\Customer\App\CustomerProductRepository;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class FetchProductNameAndImageAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class FetchProductNameAndImageAction
{
    private CustomerProductRepository $repository;
    private int                       $languageId;
    
    
    /**
     * @param CustomerProductRepository $repository
     * @param UserPreferences           $userPreferences
     */
    public function __construct(
        CustomerProductRepository $repository,
        UserPreferences           $userPreferences
    ) {
        $this->repository = $repository;
        $this->languageId = $userPreferences->languageId();
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
        $body       = $request->getParsedBody();
        $productIds = $body['productIds'];
        $productIds = array_map('intval', $productIds);
        
        return $response->withJson([
                                       'data' => $this->repository->getProductsNameAndImage($this->languageId,
                                           ...
                                                                                            $productIds)->toArray(),
                                   ]);
    }
}