<?php
/* --------------------------------------------------------------
   DeleteTrackingCodesAction.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\TrackingCode\App\Actions;

use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\DeletionOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteTrackingCodesAction
 *
 * @package Gambio\Api\Modules\TrackingCode\App\Actions
 */
class DeleteTrackingCodesAction
{
    /**
     * @var TrackingCodeWriteService
     */
    private $service;
    
    
    /**
     * DeleteTrackingCodesAction constructor.
     *
     * @param TrackingCodeWriteService $service
     */
    public function __construct(TrackingCodeWriteService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     *
     * @throws DeletionOfTrackingCodesFailedException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $ids = [];
        if ($request->getAttribute('ids') !== null) {
            foreach (explode(',', $request->getAttribute('ids')) as $id) {
                $ids[] = (int)$id;
            }
        }
        
        $this->service->deleteTrackingCodes(...$ids);
        
        return $response->withStatus(204);
    }
}