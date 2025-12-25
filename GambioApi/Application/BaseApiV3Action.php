<?php
/* --------------------------------------------------------------
   BaseApiV3Action.php 2022-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application;

use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class BaseApiV3Action
 *
 * @package Gambio\Api\Application
 */
class BaseApiV3Action
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $uri        = $request->getUri();
        $apiBaseUrl = $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath();
        
        return $response->withJson([
                                       'currencies'            => $apiBaseUrl . '/currencies',
                                       'customers'             => $apiBaseUrl . '/customers',
                                       'customer-addon-values' => $apiBaseUrl . '/customers/{customerId}/addon-values',
                                       'customer-memos'        => $apiBaseUrl . '/customers/{customerId}/memos',
                                       'image-lists'           => $apiBaseUrl . '/image-lists',
                                       'options'               => $apiBaseUrl . '/options',
                                       'parcel-services'       => $apiBaseUrl . '/parcel-services',
                                       'products-options'      => $apiBaseUrl . '/products/{productId}/options',
                                       'products-variants'     => $apiBaseUrl . '/products/{productId}/variants',
                                       'registration'          => $apiBaseUrl . '/_register-customers',
                                       'tracking-codes'        => $apiBaseUrl . '/tracking-codes',
                                       'withdrawals'           => $apiBaseUrl . '/withdrawals',
                                   ]);
    }
}