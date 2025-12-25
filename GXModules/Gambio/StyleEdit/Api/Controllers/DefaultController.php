<?php
/* --------------------------------------------------------------
  DefaultController.php 2019-12-17
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Api\Controllers;

use Exception;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Services\SettingsService;
use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Class DefaultController
 * @package Gambio\StyleEdit\Api\Controllers
 */
class DefaultController extends BasicController
{
    /**
     * @var SettingsService
     */
    protected $settingsService;
    
    /**
     * @var string
     */
    protected $themeId;
    
    
    
    /**
     * @return bool
     */
    public function requiresAuthentication(): bool
    {
        return false;
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get(array $uri)
    {
        $themeId       = end($uri);
        $configuration = $this->themeService()->getConfigurationById($themeId);

        return $this->outputJson(['success' => true]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function put(array $uri, $data)
    {
        // TODO: Implement put() method.
    }
    
    
    /**
     * @inheritDoc
     */
    public function post(array $uri, $data)
    {
        // TODO: Implement post() method.
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete(array $uri, $data)
    {
        // TODO: Implement delete() method.
    }
    
    
    /**
     * @inheritDoc
     */
    public function patch(array $uri, $data)
    {
        // TODO: Implement patch() method.
    }
    
    
    /**
     * @inheritDoc
     */
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }
    
    
    /**
     * @return SettingsService
     * @throws Exception
     */
    protected function settingsService(): SettingsService
    {
        if ($this->settingsService === null) {
            
            $this->settingsService = SingletonPrototype::instance()->get(SettingsService::class);
        }
        
        return $this->settingsService;
    }
}