<?php
/*--------------------------------------------------------------
   PatchUserConfigurationAction.php 2022-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Gambio\Admin\Modules\Customer\App\CustomerUserConfigurationRepository;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class PatchUserConfigurationAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 */
class PatchUserConfigurationAction
{
    private CustomerUserConfigurationRepository $userConfigurationRepository;
    private array $keyToMethodMap = [
        'SHOW_WARNING_ON_LOGIN_AS_CUSTOMER' => 'setShowWarningOnLoginAsCustomerValue',
        'CUSTOMERS_PER_PAGE' => 'setCustomersPerPageValue',
    ];
    
    
    /**
     * @param CustomerUserConfigurationRepository $userConfigurationRepository
     */
    public function __construct(
        CustomerUserConfigurationRepository $userConfigurationRepository
    ) {
        $this->userConfigurationRepository = $userConfigurationRepository;
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
        $body = $request->getParsedBody();
        
        if (is_array($body) === false || count($body) === 0) {
    
            return $response->withStatus(409)->withJson(['error' => 'send body is not an associative array']);
        }
    
        foreach ($body as $key => $value) {
            
            if (array_key_exists($key, $this->keyToMethodMap) === false) {
                
                return $response->withStatus(409)->withJson(['error' => sprintf('unknown configuration "%s"', $key)]);
            }
            
            $method = $this->keyToMethodMap[$key];
            
            $this->userConfigurationRepository->$method($value);
        }
        
        return $response->withStatus(204);
    }
}