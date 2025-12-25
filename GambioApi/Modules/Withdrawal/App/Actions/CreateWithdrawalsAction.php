<?php
/* --------------------------------------------------------------
   CreateWithdrawalsAction.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Withdrawal\App\Actions;

use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\CreationOfWithdrawalFailedException;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Withdrawal\App\WithdrawalApiRequestParser;
use Gambio\Api\Modules\Withdrawal\App\WithdrawalApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateWithdrawalsAction
 *
 * @package Gambio\Api\Modules\Withdrawal\App\Actions
 */
class CreateWithdrawalsAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var WithdrawalApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var WithdrawalApiRequestValidator
     */
    private $requestValidator;
    
    /**
     * @var WithdrawalWriteService
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * CreateWithdrawalsAction constructor.
     *
     * @param WithdrawalApiRequestParser    $requestParser
     * @param WithdrawalApiRequestValidator $requestValidator
     * @param WithdrawalWriteService        $service
     * @param Url                           $url
     */
    public function __construct(
        WithdrawalApiRequestParser    $requestParser,
        WithdrawalApiRequestValidator $requestValidator,
        WithdrawalWriteService        $service,
        Url                           $url
    ) {
        $this->requestParser    = $requestParser;
        $this->requestValidator = $requestValidator;
        $this->service          = $service;
        $this->url              = $url;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     *
     * @throws CreationOfWithdrawalFailedException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $errors = $this->requestValidator->validatePostRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->requestParser->parseWithdrawalDataForCreation($request, $errors);
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        $ids = $this->service->createMultipleWithdrawals(...$creationArguments);
        
        $links = [];
        foreach ($ids as $id) {
            $links[] = $this->url->restApiV3() . '/withdrawals/' . $id->value();
        }
        
        $metaData = $this->createApiMetaData($links);
        
        return $response->withJson([
                                       'data'  => $ids->toArray(),
                                       '_meta' => $metaData,
                                   ],
                                   201);
    }
}