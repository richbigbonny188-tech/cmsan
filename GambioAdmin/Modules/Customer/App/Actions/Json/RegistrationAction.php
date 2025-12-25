<?php
/*--------------------------------------------------------------
   RegistrationAction.php 2022-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerPasswordWriteService as CustomerPasswordWriteServiceInterface;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfMainAdminNotPermittedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressWriteService;
use Gambio\Admin\Modules\Customer\App\RegistrationRequestParser;
use Gambio\Admin\Modules\Customer\App\RegistrationRequestValidator;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class RegistrationAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 */
class RegistrationAction
{
    private CustomerWriteService                  $writeService;
    private RegistrationRequestValidator          $validator;
    private RegistrationRequestParser             $parser;
    private CustomerDefaultAddressWriteService    $addressWriteService;
    private CustomerPasswordWriteServiceInterface $passwordWriteService;
    private CustomerNewsletterWriteService        $newsletterWriteService;
    private UserPreferences                       $userPreferences;
    
    
    /**
     * @param CustomerWriteService                  $writeService
     * @param CustomerDefaultAddressWriteService    $addressWriteService
     * @param CustomerPasswordWriteServiceInterface $passwordWriteService
     * @param RegistrationRequestValidator          $validator
     * @param RegistrationRequestParser             $parser
     * @param CustomerNewsletterWriteService        $newsletterWriteService
     * @param UserPreferences                       $userPreferences
     */
    public function __construct(
        CustomerWriteService                  $writeService,
        CustomerDefaultAddressWriteService    $addressWriteService,
        CustomerPasswordWriteServiceInterface $passwordWriteService,
        RegistrationRequestValidator          $validator,
        RegistrationRequestParser             $parser,
        CustomerNewsletterWriteService        $newsletterWriteService,
        UserPreferences                       $userPreferences
    ) {
        $this->writeService           = $writeService;
        $this->addressWriteService    = $addressWriteService;
        $this->passwordWriteService   = $passwordWriteService;
        $this->validator              = $validator;
        $this->parser                 = $parser;
        $this->newsletterWriteService = $newsletterWriteService;
        $this->userPreferences        = $userPreferences;
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
        $errors = $this->validator->validateRegistrationRequest($request->getParsedBody());
    
        if (empty($errors) === false) {
        
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
    
        $creationArguments = $this->parser->parseCustomerDataForRegistration($request, $errors);
        
        if (empty($creationArguments) === true) {
            
            $errors[] = 'no accounts to create in payload';
        }
        
        if (count($errors) > 0) {
        
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $ids = $this->writeService->createMultipleCustomers(... array_column($creationArguments, 'customer'))
                ->toArray();
            $arguments = array_map(fn(int $i) => array_merge($creationArguments[$i], ['id' => $ids[$i]]),
                range(0, count($ids) - 1));
    
            foreach ($arguments as ['id' => $id, 'address' => $address, 'password' => $password, 'customer' => $customer, 'isSubscribed' => $isSubscribed]) {
                // The last position of the "customer" array contains the "isGuestAccount", which has been sent by the
                // front-end and defined in $this->parser->parseCustomerDataForRegistration
                $isGuestAccount = end($customer);
    
                // Guests does not have password
                if (!$isGuestAccount) {
                    $this->passwordWriteService->setCustomerPassword((int)$id, $password);
                }
                $this->addressWriteService->createCustomerAddress(...array_merge([$id], $address));
    
                if ($isSubscribed) {
                    $adminId = $this->userPreferences->userId();
                    $this->newsletterWriteService->subscribe($id, $adminId);
                }
            }
            
            return $response->withStatus(200)->withJson($ids);
            
        } catch (Exception $exception) {
    
            if (isset($ids) && count($ids)) {
                try {
                    $this->writeService->deleteCustomers(...$ids);
                } catch (DeletionOfCustomerFailedException|DeletionOfMainAdminNotPermittedException $e) {
                    unset($e);
                }
            }
    
            $emailInUse = $exception instanceof CustomerEmailAddressMustBeUniqueException;
            
            return $response->withStatus($emailInUse ? 409 : 422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}