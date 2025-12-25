<?php
/*--------------------------------------------------------------
   FetchAllImageListsAction.php 2021-05-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App\Actions;

use Gambio\Admin\Modules\ImageList\Services\ImageListFilterService as ImageListFilterServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllImageListsAction
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class FetchAllImageListsAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    /**
     * @var ImageListApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var ImageListFilterServiceInterface
     */
    private $service;
    
    
    /**
     * FetchAllImageListsAction constructor.
     *
     * @param ImageListApiRequestParser       $requestParser
     * @param ImageListFilterServiceInterface $service
     */
    public function __construct(
        ImageListApiRequestParser $requestParser,
        ImageListFilterServiceInterface $service
    ){
        $this->requestParser = $requestParser;
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
        $fields  = $this->requestParser->getFields($request);
        $filters = $this->requestParser->getFilters($request);
        $sorting = $this->requestParser->getSorting($request);
        $page    = $this->requestParser->getPage($request);
        $limit   = $this->requestParser->getPerPage($request);
        $offset  = $limit * ($page - 1);
        
        $imageLists = $this->service->filterImageLists($filters, $sorting, $limit, $offset);
        $totalItems = $this->service->getImageListsTotalCount($filters);
    
        $metaData   = $this->createApiCollectionMetaData($page,
                                                         $limit,
                                                         $totalItems,
                                                         $this->requestParser->getResourceUrlFromRequest($request),
                                                         $request->getQueryParams());
    
        $responseData = $imageLists->toArray();
        if (count($fields) > 0) {
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
        foreach ($responseData as $index => $documentData) {
            unset($responseData[$index]['newValues']);
        }
    
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => $metaData,
                                   ]);
    }
}