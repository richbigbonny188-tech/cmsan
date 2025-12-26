<?php
/* --------------------------------------------------------------
   ParcelServiceController.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App;

use Exception;
use Gambio\Admin\Application\Http\Controller\JSEngineController;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceDescription;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFactory;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFilterService;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceReadService;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceWriteService;
use Gambio\Core\Application\Http\HttpRequest;
use Gambio\Core\Application\Http\Response;

/**
 * Class ParcelServiceController
 *
 * @package Gambio\Admin\Modules\ParcelService
 * @codeCoverageIgnore
 */
class ParcelServiceController extends JSEngineController
{
    /**
     * @var ParcelServiceReadService
     */
    private $readService;
    
    /**
     * @var ParcelServiceFilterService
     */
    private $filterService;
    
    /**
     * @var ParcelServiceWriteService
     */
    private $writeService;
    
    /**
     * @var ParcelServiceFactory
     */
    private $factory;
    
    
    /**
     * ParcelServiceController constructor.
     *
     * @param ParcelServiceReadService   $readService
     * @param ParcelServiceFilterService $filterService
     * @param ParcelServiceWriteService  $writeService
     * @param ParcelServiceFactory       $factory
     */
    public function __construct(
        ParcelServiceReadService   $readService,
        ParcelServiceFilterService $filterService,
        ParcelServiceWriteService  $writeService,
        ParcelServiceFactory       $factory
    ) {
        $this->readService   = $readService;
        $this->filterService = $filterService;
        $this->writeService  = $writeService;
        $this->factory       = $factory;
    }
    
    
    /**
     * @param HttpRequest $request
     * @param Response    $response
     *
     * @return Response
     * @throws Exception
     */
    public function showOverview(HttpRequest $request, Response $response): Response
    {
        $this->addJsSectionTranslation('parcel_services');
        $this->addJsSectionTranslation('buttons');
        
        $template = $this->render($this->translate('title', 'parcel_services'), __DIR__ . '/../ui/overview.html');
        
        return $response->write($template);
    }
    
    
    /**
     * @param HttpRequest $request
     * @param Response    $response
     *
     * @return Response
     */
    public function getParcelServices(HttpRequest $request, Response $response): Response
    {
        $_SESSION['coo_page_token']->is_valid($request->getQueryParam('page_token'));
        
        $limit  = (int)$request->getQueryParam('limit', 10);
        $limit  = ($limit <= 0) ? 10 : $limit;
        $offset = (int)$request->getQueryParam('offset', 0);
        $offset = ($offset < 0) ? 0 : $offset;
        $order  = ($request->getQueryParam('order') === 'desc') ? '-name' : 'name';
        
        $parcelServices = $this->filterService->filterParcelServices([], $order, $limit, $offset);
        $totalItems     = $this->filterService->getParcelServicesTotalCount([]);
        
        return $response->withJson([
                                       'success'        => true,
                                       'parcelServices' => $parcelServices->toArray(),
                                       'totalItems'     => $totalItems,
                                   ]);
    }
    
    
    /**
     * @param HttpRequest $request
     * @param Response    $response
     *
     * @return Response
     */
    public function createParcelService(HttpRequest $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
            
            $data['descriptions'] = array_map(function (array $data): ParcelServiceDescription {
                return $this->factory->createParcelServiceDescription($data['languageCode'],
                                                                      $data['url'],
                                                                      $data['comment']);
            },
                array_values($data['descriptions']));
            
            $this->writeService->createParcelService($data['name'],
                                                     $this->factory->createParcelServiceDescriptions(...
                                                         $data['descriptions']),
                                                     $data['isDefault'] === 'true',
                                                     $data['shipmentType']);
        } catch (Exception $e) {
            return $response->withJson(['success' => false, 'error' => 'Invalid request body.'], 400);
        }
        
        return $response->withJson(['success' => true]);
    }
    
    
    /**
     * @param HttpRequest $request
     * @param Response    $response
     *
     * @return Response
     */
    public function updateParcelService(HttpRequest $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
            
            $data['descriptions'] = array_map(function (array $data): ParcelServiceDescription {
                return $this->factory->createParcelServiceDescription($data['languageCode'],
                                                                      $data['url'],
                                                                      $data['comment']);
            },
                array_values($data['descriptions']));
            
            $parcelService = $this->readService->getParcelServiceById((int)$data['id']);
            if ($data['isDefault'] === 'true') {
                $parcelService->setAsDefault();
            }
            $parcelService->changeName($data['name']);
            $parcelService->changeShipmentType($data['shipmentType']);
            $parcelService->changeDescriptions($this->factory->createParcelServiceDescriptions(...
                $data['descriptions']));
            
            $this->writeService->storeParcelServices($parcelService);
        } catch (Exception $e) {
            return $response->withJson(['success' => false, 'error' => 'Invalid request body.'], 400);
        }
        
        return $response->withJson(['success' => true]);
    }
    
    
    /**
     * @param HttpRequest $request
     * @param Response    $response
     *
     * @return Response
     */
    public function deleteParcelService(HttpRequest $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
        
        try {
            $id = (int)$request->getAttribute('id');
            if ($id <= 0) {
                return $response->withJson(['success' => false, 'error' => 'Invalid ID provided.'], 422);
            }
            
            $this->writeService->deleteParcelServices($id);
        } catch (Exception $e) {
            return $response->withJson(['success' => false, 'error' => 'Invalid request body.'], 400);
        }
        
        return $response->withJson(['success' => true]);
    }
}