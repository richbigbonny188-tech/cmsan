<?php
/*--------------------------------------------------------------
   NextCustomerNumberAction.php 2022-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Gambio\Admin\Modules\Customer\App\NextCustomerNumberRepository;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class NextCustomerNumberAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 */
class NextCustomerNumberAction
{
    private NextCustomerNumberRepository $repository;
    
    
    /**
     * @param NextCustomerNumberRepository $repository
     */
    public function __construct(NextCustomerNumberRepository $repository)
    {
        $this->repository = $repository;
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
        return $response->withJson(['data' => $this->repository->next()]);
    }
}