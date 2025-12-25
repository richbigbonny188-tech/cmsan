<?php
/* --------------------------------------------------------------
 UserConfigurationController.php 2021-05-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserConfiguration\App;

use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\UserConfiguration\Services\CurrentUserConfigurationService as CurrentUserConfigurationServiceInterface;
use Throwable;

/**
 * Class UserConfigurationController
 * @package Gambio\Admin\Modules\UserConfiguration
 */
class UserConfigurationController
{
    /**
     * @var CurrentUserConfigurationServiceInterface
     */
    private $configurationService;
    
    
    /**
     * UserConfigurationController constructor.
     *
     * @param CurrentUserConfigurationServiceInterface $configurationService
     */
    public function __construct(CurrentUserConfigurationServiceInterface $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * Returns a user configuration value for currently logged in user.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function get(Request $request, Response $response): Response
    {
        $key   = $request->getQueryParam('key', 'key');
        $value = $this->configurationService->getValue($key);
        
        $success = $value !== null;
        
        return $response->withJson(['success' => $success, 'value' => $value]);
    }
    
    
    /**
     * Saves a user configuration for the currently logged in user.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function save(Request $request, Response $response): Response
    {
        try {
            $key   = $request->getParsedBodyParam('key');
            $value = $request->getParsedBodyParam('value');
            
            $this->configurationService->storeConfiguration($key, $value);
            
            $data = ['success' => true];
        } catch (Throwable $e) {
            $data = ['success' => false, 'message' => $e->getMessage()];
        }
        
        return $response->withJson($data);
    }
}