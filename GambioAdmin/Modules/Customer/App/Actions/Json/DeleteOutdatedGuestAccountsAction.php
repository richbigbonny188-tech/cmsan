<?php
/*--------------------------------------------------------------
   DeleteOutdatedGuestAccountsAction.php 2022-05-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteOutdatedGuestAccountsAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class DeleteOutdatedGuestAccountsAction
{
    private CustomerWriteService $service;
    
    
    /**
     * @param CustomerWriteService $service
     */
    public function __construct(CustomerWriteService $service)
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
            $this->service->deleteOutdatedGuestAccounts();
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(409)->withJson(['errors' => $exception->getMessage()]);
        }
    }
}