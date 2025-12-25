<?php
/* --------------------------------------------------------------
  ThemeExtensionController.php 2019-10-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Api\Controllers;

use Faker\Provider\File;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Components\Theme\StyleEditThemeService;
use Gambio\StyleEdit\Core\Helpers\ConvertSettingsToDefaultValueThemeExtensions;
use Gambio\StyleEdit\Core\Helpers\DefaultOverwriteCreator;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;

/**
 * Class ThemeExtensionController
 */
class ThemeExtensionController extends BasicController
{
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    
    /**
     * @param array $uri
     *
     * @return mixed
     * @throws TranslatedException
     * @throws FileNotFoundException
     */
    public function get(array $uri)
    {
        $themeService   = $this->themeService();
        $themeId        = end($uri);
        $theme          = $themeService->getConfigurationById($themeId);
        $themeStd       = json_decode(json_encode($theme, JSON_PRETTY_PRINT), false);
        $settingJsonStr = $this->themeFilesystem()->read($themeId . DIRECTORY_SEPARATOR . 'settings.json');
        $settingJson    = json_decode($settingJsonStr);
        
        $converter        = new ConvertSettingsToDefaultValueThemeExtensions($themeStd, $settingJson);
        $overwriteCreator = new DefaultOverwriteCreator($themeId, $this->themeFilesystem(), $converter);
        $overwriteCreator->store();
        
        return $this->outputJson((object)['success' => true]);
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
     */
    public function post(array $uri, $data)
    {
        // TODO: Implement post() method.
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
    
    
    protected function themeFilesystem(): FilesystemAdapter
    {
        if ($this->filesystem === null) {
            
            $this->filesystem = SingletonPrototype::instance()->get(FilesystemAdapter::class);
        }
        
        return $this->filesystem;
    }
    
    
    /**
     * @return bool
     */
    public function requiresAuthentication(): bool
    {
        return false;
    }
}