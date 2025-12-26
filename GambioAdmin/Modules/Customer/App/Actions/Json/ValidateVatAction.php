<?php
/*--------------------------------------------------------------
   ValidateVatAction.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\VatValidation\Services\VatValidationService as VatValidationServiceInterface;

/**
 * Class ValidateVatAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class ValidateVatAction
{
    private VatValidationServiceInterface $service;
    
    
    /**
     * @param VatValidationServiceInterface $service
     */
    public function __construct(VatValidationServiceInterface $service)
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
        $body  = $request->getParsedBody();
        $vatId = $body['vatId'];
        
        $isValid = $this->service->validateVatId($vatId);
        
        return $response->withStatus(200)->withJson(['isValid' => $isValid]);
    }
}