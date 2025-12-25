<?php
/*--------------------------------------------------------------
   CustomerMemoApiRequestParser.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\Memos\App;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;
use Gambio\Api\Application\Auth\Interfaces\WebRequestUserIdentificationService;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CustomerMemoApiRequestParser
 *
 * @package Gambio\Api\Modules\Customer\Submodules\Memos\App
 */
class CustomerMemoApiRequestParser
{
    public const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';
    private WebRequestUserIdentificationService $userIdentificationService;
    private CustomerMemoFactory                 $factory;
    
    
    /**
     * @param WebRequestUserIdentificationService $userIdentificationService
     * @param CustomerMemoFactory                 $factory
     */
    public function __construct(
        WebRequestUserIdentificationService $userIdentificationService,
        CustomerMemoFactory                 $factory
    ) {
        $this->userIdentificationService = $userIdentificationService;
        $this->factory                   = $factory;
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getLimit(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('limit', 100);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getOffset(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('offset', 0);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getPage(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('page', 1);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getPerPage(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('per-page', 25);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFields(ServerRequestInterface $request): array
    {
        $fields = $request->getQueryParam('fields');
        
        return ($fields === null) ? [] : explode(',', $fields);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFilters(ServerRequestInterface $request): array
    {
        return $request->getQueryParam('filter', []);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function getSorting(ServerRequestInterface $request): ?string
    {
        return $request->getQueryParam('sort');
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getResourceUrlFromRequest(ServerRequestInterface $request): string
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getPath();
    }
    
    
    /**
     * @param ServerRequestInterface $request
     * @param int             $customerId
     * @param array                  $errors
     *
     * @return array
     */
    public function parseCustomerMemoDataForCreation(
        ServerRequestInterface $request,
        int             $customerId,
        array                  &$errors = []
    ): array {
        $creationArguments = [];
    
        foreach ($request->getParsedBody() as $documentData) {
            
            $creationArguments[] = [
                $customerId,
                isset($documentData['creatorId']) ? (int)$documentData['creatorId'] : $this->creatorIdFromSendAuthorisation($request),
                $documentData['content'],
            ];
        }
        
        return $creationArguments;
    }
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    private function creatorIdFromSendAuthorisation(ServerRequestInterface $request): int
    {
        return $this->userIdentificationService->identifyUser($request);
    }
}