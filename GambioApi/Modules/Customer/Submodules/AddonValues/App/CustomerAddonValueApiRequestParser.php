<?php
/*--------------------------------------------------------------
   CustomerAddonValueApiRequestParser.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Webmozart\Assert\Assert;

/**
 * Class CustomerAddonValueApiRequestParser
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues\App
 */
class CustomerAddonValueApiRequestParser
{
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
     * @param int                    $customerId
     * @param array                  $errors
     *
     * @return array
     */
    public function parseCustomerAddonValueDataForCreation(
        ServerRequestInterface $request,
        int                    $customerId,
        array                  &$errors = []
    ): array {
        $creationArguments = [];
        
        foreach ($request->getParsedBody() as $index => $documentData) {
            try {
                Assert::string($documentData['key'], 'Key need to be a string.');
                Assert::notWhitespaceOnly($documentData['key'], 'Key can not be whitespace only.');
                Assert::string($documentData['value'], 'Value need to be a string.');
    
                $creationArguments[] = [$customerId, $documentData['key'], $documentData['value']];
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
    
        return $creationArguments;
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function parseCustomerAddonValueIdsForDeletion(ServerRequestInterface $request): array
    {
        $customerAddonValueIds = [];
    
        $customerId = (int)$request->getAttribute('customerId');
        if ($request->getAttribute('keys') !== null) {
            foreach (explode(',', $request->getAttribute('keys')) as $key) {
                $customerAddonValueIds[] = [
                    'customerId' => $customerId,
                    'key'        => $key,
                ];
            }
        }
    
        return $customerAddonValueIds;
    }
}