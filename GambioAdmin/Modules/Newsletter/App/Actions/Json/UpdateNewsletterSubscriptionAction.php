<?php
/* --------------------------------------------------------------
   UpdateNewsletterSubscriptionAction.php 2022-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class UpdateNewsletterSubscriptionAction
 *
 * @package Gambio\Admin\Modules\Newsletter\App\Actions\Json
 */
class UpdateNewsletterSubscriptionAction
{
    private CustomerNewsletterWriteService $writeService;
    private int                           $adminId;
    
    
    /**
     * @param CustomerNewsletterWriteService $writeService
     * @param UserPreferences                $preferences
     */
    public function __construct(CustomerNewsletterWriteService $writeService, UserPreferences $preferences)
    {
        $this->writeService = $writeService;
        $this->adminId      = $preferences->userId();
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
    
            foreach ($request->getParsedBody() as ['customerId' => $customerId, 'subscribed' => $subscribed]) {
    
                if ($customerId <= 0) {
                    
                    $errorMessage = sprintf('Invalid customer ID given. Got: %s', $customerId);
                    return $response->withJson(['error' => $errorMessage], 400);
                }
    
                if ($subscribed) {
                    $this->writeService->subscribe($customerId, $this->adminId);
                } else {
                    $this->writeService->unsubscribe($customerId);
                }
            }
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withJson(['error' => $exception->getMessage()], 422);
        }
    }
}