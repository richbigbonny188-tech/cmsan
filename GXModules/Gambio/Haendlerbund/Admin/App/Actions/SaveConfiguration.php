<?php
/* --------------------------------------------------------------
   SaveConfiguration.php 2022-03-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\App\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;

class SaveConfiguration extends AdminModuleAction
{
    private const PREFIX = 'modules/GambioHaendlerbundAdminModule/';
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function handle(Request $request, Response $response): Response
    {
        $newConfiguration  = $request->getParsedBody();
        $configuration = [
            'active'                => $newConfiguration['active'] === '1' ? '1' : '0',
            'accessToken'           => $newConfiguration['accessToken'],
            'mode'                  => $newConfiguration['mode'] === 'develop' ? 'develop' : 'productive',
            'useTos'                => isset($newConfiguration['useTos']) ? 'true' : 'false',
            'usePrivacy'            => isset($newConfiguration['usePrivacy']) ? 'true' : 'false',
            'useImprint'            => isset($newConfiguration['useImprint']) ? 'true' : 'false',
            'usePaymentAndShipping' => isset($newConfiguration['usePaymentAndShipping']) ? 'true' : 'false',
            'useWithdrawal'         => isset($newConfiguration['useWithdrawal']) ? 'true' : 'false',
        ];
        foreach ($configuration as $key => $value) {
            $this->configurationService->save(static::PREFIX . $key, $value);
        }
    
        $_SESSION['haendlerbund_message'] = [
            'message' => 'configuration_saved',
        ];
        $showConfigurationUrl = $this->url->admin() . '/haendlerbund/configuration';
        return $response->withRedirect($showConfigurationUrl, 302);
    }
}