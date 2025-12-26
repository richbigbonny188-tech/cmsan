<?php
/* --------------------------------------------------------------
   CreateOptionValuesAction.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Option\App\OptionApiRequestParser;
use Gambio\Api\Modules\Option\App\OptionApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateOptionValuesAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class CreateOptionValuesAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var OptionApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var OptionApiRequestValidator
     */
    private $requestValidator;
    
    /**
     * @var OptionReadService
     */
    private $readService;
    
    /**
     * @var OptionWriteService
     */
    private $writeService;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * CreateOptionValuesAction constructor.
     *
     * @param OptionApiRequestParser    $requestParser
     * @param OptionApiRequestValidator $requestValidator
     * @param OptionReadService         $readService
     * @param OptionWriteService        $writeService
     * @param Url                       $url
     */
    public function __construct(
        OptionApiRequestParser    $requestParser,
        OptionApiRequestValidator $requestValidator,
        OptionReadService         $readService,
        OptionWriteService        $writeService,
        Url                       $url
    ) {
        $this->requestParser    = $requestParser;
        $this->requestValidator = $requestValidator;
        $this->readService      = $readService;
        $this->writeService     = $writeService;
        $this->url              = $url;
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
        $errors = $this->requestValidator->validateOptionValuePostRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $optionValues = $this->requestParser->parseOptionValueDataForCreation($request, $errors);
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->readService->getOptionById($optionId);
            $option->addNewValues(...$optionValues);
            $this->writeService->storeOptions($option);
        } catch (OptionDoesNotExistException $e) {
            return $response->withStatus(404);
        }
        
        $option            = $this->readService->getOptionById($optionId); // Load option again to fetch IDs of added values
        $optionValueIds    = [];
        $optionValuesArray = $option->values()->toArray();
        
        // Sorting by the ID
        uasort($optionValuesArray, static function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });
        
        foreach (array_slice($optionValuesArray, -count($optionValues)) as $index => $optionValue) {
            $optionValueIds[] = $optionValue['id'];
        }
        
        $links = [];
        foreach ($optionValueIds as $id) {
            $links[] = $this->url->restApiV3() . '/options/' . $optionId . '/values/' . $id;
        }
        
        $metaData = $this->createApiMetaData($links);
        
        return $response->withJson([
                                       'data'  => $optionValueIds,
                                       '_meta' => $metaData,
                                   ],
                                   201);
    }
}