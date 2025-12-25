<?php
/* --------------------------------------------------------------
   FetchSpecificOptionAction.php 2020-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchSpecificOptionAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class FetchSpecificOptionAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var OptionReadService
     */
    private $service;
    
    
    /**
     * FetchSpecificOptionAction constructor.
     *
     * @param OptionReadService $service
     */
    public function __construct(OptionReadService $service)
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
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->service->getOptionById($optionId)->toArray();
            $metaData = $this->createApiMetaData();
            unset($option['newValues']);
            
            return $response->withJson([
                                           'data'  => $option,
                                           '_meta' => $metaData,
                                       ]);
        } catch (OptionDoesNotExistException $e) {
            return $response->withStatus(404);
        }
    }
}