<?php
/*--------------------------------------------------------------
   FetchLogAdminActivitiesStatusAction.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Exception;
use Gambio\Admin\Modules\Customer\App\LogAdminActivityRepository;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchLogAdminActivitiesStatusAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class FetchLogAdminActivitiesStatusAction
{
    private LogAdminActivityRepository $repository;
    
    
    /**
     * @param LogAdminActivityRepository $repository
     */
    public function __construct(LogAdminActivityRepository $repository)
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
        try {
            $customerId = (int)$request->getAttribute('customerId');
            $status     = $this->repository->getLogAdminActivitiesStatus($customerId);
            
            return $response->withJson(['status' => $status]);
            
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['error' => $exception->getMessage()]);
        }
    }
}