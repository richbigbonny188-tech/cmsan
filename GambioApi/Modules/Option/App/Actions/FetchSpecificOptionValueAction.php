<?php
/* --------------------------------------------------------------
   FetchSpecificOptionValueAction.php 2022-03-24
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
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class FetchSpecificOptionValueAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class FetchSpecificOptionValueAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var OptionReadService
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * FetchSpecificOptionValueAction constructor.
     *
     * @param OptionReadService $service
     * @param Url               $url
     */
    public function __construct(OptionReadService $service, Url $url)
    {
        $this->service = $service;
        $this->url     = $url;
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
            $optionId      = (int)$request->getAttribute('optionId');
            $optionValueId = (int)$request->getAttribute('optionValueId');
            $option        = $this->service->getOptionById($optionId);
            
            foreach ($option->values() as $optionValue) {
                if ($optionValue->id() === $optionValueId) {
                    $links = [
                        'option' => $this->url->restApiV3() . '/options/' . $optionId,
                    ];
                    
                    return $response->withJson([
                                                   'data'  => $optionValue->toArray(),
                                                   '_meta' => $this->createApiMetaData($links),
                                               ]);
                }
            }
            
            return $response->withStatus(404);
        } catch (OptionDoesNotExistException $e) {
            return $response->withStatus(404);
        }
    }
}