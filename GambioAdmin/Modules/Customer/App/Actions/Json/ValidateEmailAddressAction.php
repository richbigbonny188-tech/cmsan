<?php
/*--------------------------------------------------------------
   ValidateEmailAddressAction.php 2022-10-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\EmailAddressIsInvalidException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class ValidateEmailAddressAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class ValidateEmailAddressAction
{
    /**
     * @var CustomerReadService
     */
    private CustomerReadService $service;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * @param CustomerReadService $service
     */
    public function __construct(CustomerReadService $service, TextManager $textManager)
    {
        $this->service = $service;
        $this->textManager = $textManager;
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
        $body  = $request->getParsedBody();
        $email = $body['email'];
        
        try {
            $data = ['success' => $this->service->validateEmailAddress($email)];
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            
            if ($exception instanceof EmailAddressIsInvalidException) {
                $message = $this->textManager->getPhraseText('profile_error_invalid_email', 'customer');
            } elseif ($exception instanceof CustomerEmailAddressMustBeUniqueException) {
                $message = $this->textManager->getPhraseText('profile_error_email_already_exists', 'customer');
            }
            
            $data = [
                'success'   => false,
                'errorCode' => $exception->getCode(),
                'message'   => $message,
            ];
        }
        
        return $response->withStatus(200)->withJson($data);
    }
}