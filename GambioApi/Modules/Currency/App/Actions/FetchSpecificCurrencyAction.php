<?php
/*--------------------------------------------------------------
   FetchSpecificCurrencyAction.php 2022-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Currency\App\Actions;

use Gambio\Admin\Modules\Currency\Services\CurrencyReadService as CurrencyReadServiceInterface;
use Gambio\Admin\Modules\Currency\Services\Exceptions\CurrencyDoesNotExistException;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchSpecificCurrencyAction
 *
 * @package Gambio\Api\Modules\Currency\App\Actions
 */
class FetchSpecificCurrencyAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var CurrencyReadServiceInterface
     */
    private $service;
    
    
    /**
     * @param CurrencyReadServiceInterface $service
     */
    public function __construct(CurrencyReadServiceInterface $service)
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
        try {
            $currencyId = (int)$request->getAttribute('currencyId');
            $currency = $this->service->getCurrencyById($currencyId);
    
            return $response->withJson([
                                           'data'  => $currency->toArray(),
                                           '_meta' => $this->createApiMetaData(),
                                       ]);
        } catch (CurrencyDoesNotExistException $exception) {
            return $response->withStatus(404);
        }
    }
}