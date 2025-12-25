<?php
/*--------------------------------------------------------------
   ChangePasswordAction.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Gambio\Admin\Modules\Customer\Services\CustomerPasswordWriteService as CustomerPasswordWriteServiceInterface;
use Gambio\Admin\Modules\Customer\Services\Exceptions\InvalidPasswordFormatException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use InvalidArgumentException;

/**
 * Class ChangePasswordAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class ChangePasswordAction
{
    private CustomerPasswordWriteServiceInterface $passwordWriteService;
    
    
    /**
     * @param CustomerPasswordWriteServiceInterface $passwordWriteService
     */
    public function __construct(
        CustomerPasswordWriteServiceInterface $passwordWriteService
    ) {
        $this->passwordWriteService = $passwordWriteService;
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
            $body       = $request->getParsedBody();
            $customerId = (int)$body['customerId'];
            
            if ($customerId <= 0) {
                
                throw new InvalidArgumentException('Invalid customer ID given. Got: ' . $body['customerId']);
            }
            
            if (isset($body['password']) === false || ($password = $body['password']) === '') {
    
                throw new InvalidArgumentException('No valid password provided . Got: ' . ($password ?? ''));
            }
            
            $this->passwordWriteService->setCustomerPassword($customerId, $password);
            
            return $response->withStatus(201);
            
        } catch (InvalidArgumentException $exception) {
    
            return $response->withStatus(400)->withJson(['errors' => $exception->getMessage(),]);
        } catch (InvalidPasswordFormatException $exception) {
            
            return $response->withStatus(422)->withJson(['errors' => $exception->getMessage()]);
        }
    }
}