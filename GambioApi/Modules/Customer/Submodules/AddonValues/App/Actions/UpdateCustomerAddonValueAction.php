<?php
/*--------------------------------------------------------------
   UpdateCustomerAddonValueAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueReadService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueDoesNotExistException;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Webmozart\Assert\Assert;

/**
 * Class UpdateCustomerAddonValueAction
 *
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class UpdateCustomerAddonValueAction
{
    private CustomerAddonValueReadService         $readService;
    private CustomerAddonValueWriteService        $writeService;
    private CustomerAddonValueApiRequestValidator $validator;
    
    
    /**
     * @param CustomerAddonValueReadService         $readService
     * @param CustomerAddonValueWriteService        $writeService
     * @param CustomerAddonValueApiRequestValidator $validator
     */
    public function __construct(
        CustomerAddonValueReadService         $readService,
        CustomerAddonValueWriteService        $writeService,
        CustomerAddonValueApiRequestValidator $validator
    ) {
        $this->readService  = $readService;
        $this->writeService = $writeService;
        $this->validator    = $validator;
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
        $customerId = (int)$request->getAttribute('customerId');
        $errors     = $this->validator->validateUpdateRequest($parsedBody = $request->getParsedBody());
        
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        foreach ($request->getParsedBody() as $index => $documentData) {
            try {
                Assert::string($documentData['key'], 'Key need to be a string.');
                Assert::notWhitespaceOnly($documentData['key'], 'Key can not be whitespace only.');
                Assert::string($documentData['value'], 'Value need to be a string.');
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
        if (empty($errors) === false) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $addonValues = [];
            foreach ($parsedBody as ['key' => $key, 'value' => $value]) {
                $addonValues[] = $addonValue = $this->readService->getCustomerAddonValue($customerId, $key);
                $addonValue->changeValue($value);
            }
            
            $this->writeService->storeCustomerAddonValues(...$addonValues);
            
            return $response->withStatus(204);
        } catch (CustomerAddonValueDoesNotExistException $exception) {
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}