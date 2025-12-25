<?php
/* --------------------------------------------------------------
  ConfigurationController.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Api\Controllers;

use Exception;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Services\Configuration\ConfigurationService;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Interfaces\ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Interfaces\StyleEdit3ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\StyleEdit3ConfigurationService;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use ReflectionException;

/**
 * Class ConfigurationController
 */
class ConfigurationController extends BasicController
{
    /**
     * @var StyleEdit3ConfigurationServiceInterface
     */
    protected $styleEdit3ConfigurationService;
    
    /**
     * @var ConfigurationServiceInterface
     */
    protected $configurationService;
    
    
    /**
     * @param array $uri
     *
     * @return mixed
     * @throws ReflectionException
     * @throws TranslatedException
     * @throws Exception
     */
    public function get(array $uri)
    {
        if (count($uri) === 4 && strtolower(end($uri)) === 'styleedit3') {
            
            return $this->outputJson($this->styleEdit3ConfigurationService()->configurations());
        } elseif (count($uri) === 3) {
            
            $result = $this->themeConfiguration(end($uri));
            
            return $this->outputJson($result);
        }
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function put(array $uri, $data)
    {
        // TODO: Implement put() method.
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     * @throws Exception
     */
    public function post(array $uri, $data)
    {
        if (strtolower(end($uri)) === 'styleedit3') {
            
            $data = json_decode($data, false);
            
            $styleEdit3Configuration = $this->styleEdit3ConfigurationService()->getConfigurationByPath($data->path);
            $configuration           = $this->configurationService()->convertFromStyleEdit3($styleEdit3Configuration);
            
            return $this->outputJson($configuration);
        }
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function delete(array $uri, $data)
    {
        // TODO: Implement delete() method.
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function patch(array $uri, $data)
    {
        // TODO: Implement patch() method.
    }
    
    
    /**
     * @return BasicController
     */
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }
    
    
    /**
     * @param string $themeId
     *
     * @return ConfigurationInterface
     * @throws TranslatedException
     * @throws ReflectionException
     * @throws Exception
     */
    protected function themeConfiguration(string $themeId): ConfigurationInterface
    {
        return $this->configurationService()->getSettingsById($themeId);
    }
    
    
    /**
     * @return StyleEdit3ConfigurationService
     * @throws Exception
     */
    public function styleEdit3ConfigurationService(): StyleEdit3ConfigurationService
    {
        if ($this->styleEdit3ConfigurationService === null) {
            
            $this->styleEdit3ConfigurationService = SingletonPrototype::instance()
                ->get(StyleEdit3ConfigurationService::class);
        }
        
        return $this->styleEdit3ConfigurationService;
    }
    
    
    /**
     * @return ConfigurationServiceInterface
     * @throws Exception
     */
    public function configurationService(): ConfigurationServiceInterface
    {
        if ($this->configurationService === null) {
            
            $this->configurationService = SingletonPrototype::instance()->get(ConfigurationService::class);
        }
        
        return $this->configurationService;
    }
}