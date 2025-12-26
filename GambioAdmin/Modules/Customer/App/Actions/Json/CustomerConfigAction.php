<?php
/*--------------------------------------------------------------
   CustomerConfigAction.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Gambio\Admin\Modules\Customer\App\CustomerConfigurationProvider;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CustomerConfigAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class CustomerConfigAction
{
    private CustomerConfigurationProvider $provider;
    
    
    /**
     * @param CustomerConfigurationProvider $provider
     */
    public function __construct(CustomerConfigurationProvider $provider)
    {
        $this->provider = $provider;
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
        return $response->withJson($this->provider->getConfigurations());
    }
}